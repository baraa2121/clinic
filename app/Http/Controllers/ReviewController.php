<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{
    /**
     * GET /user/reviews  OR  GET /doctor/reviews  OR  GET /admin/reviews
     * عرض التقييمات — المريض يشوف تقييماته، الدكتور يشوف تقييمات بروفايله، الأدمن يشوف الكل
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            $reviews = Review::with(['doctor.user', 'patient.user'])
                ->latest()
                ->paginate(15);
        } elseif ($user->role === 'doctor') {
            $reviews = Review::with(['patient.user'])
                ->whereHas('doctor', fn($q) => $q->where('user_id', $user->id))
                ->latest()
                ->paginate(15);
        } else {
            // patient
            $patient = Patient::where('user_id', $user->id)->first();
            $reviews = Review::with(['doctor.user'])
                ->where('patient_id', optional($patient)->id)
                ->latest()
                ->paginate(15);
        }

        return response()->json([
            'status' => true,
            'data'   => $reviews
        ], Response::HTTP_OK);
    }

    /**
     * GET /user/doctors/{doctor}/reviews  OR  /doctor/doctors/{doctor}/reviews
     * عرض تقييمات طبيب معين
     */
    public function doctorReviews($doctorId)
    {
        $reviews = Review::with(['patient.user'])
            ->where('doctor_id', $doctorId)
            ->latest()
            ->paginate(15);

        $avg = Review::where('doctor_id', $doctorId)->avg('rating');

        return response()->json([
            'status'         => true,
            'average_rating' => round($avg, 2),
            'data'           => $reviews
        ], Response::HTTP_OK);
    }

    /**
     * POST /user/reviews
     * إضافة تقييم جديد (المريض فقط)
     * شرط: يجب أن يكون المريض قد أكمل موعداً مع هذا الطبيب
     */
    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'doctor_id' => 'required|exists:doctors,id',
            'rating'    => 'required|integer|min:1|max:5',
            'comment'   => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $patient = Patient::where('user_id', $request->user()->id)->first();

        if (!$patient) {
            return response()->json([
                'status'  => false,
                'message' => 'Patient profile not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // تحقق أن المريض أكمل موعداً مع هذا الطبيب
        $hasCompleted = Appointment::where('patient_id', $patient->id)
            ->where('doctor_id', $request->doctor_id)
            ->where('status', 'completed')
            ->exists();

        if (!$hasCompleted) {
            return response()->json([
                'status'  => false,
                'message' => 'You can only review doctors after completing an appointment'
            ], Response::HTTP_FORBIDDEN);
        }

        // تحقق أنه لم يراجع من قبل
        $alreadyReviewed = Review::where('patient_id', $patient->id)
            ->where('doctor_id', $request->doctor_id)
            ->exists();

        if ($alreadyReviewed) {
            return response()->json([
                'status'  => false,
                'message' => 'You have already reviewed this doctor'
            ], Response::HTTP_CONFLICT);
        }

        $review = Review::create([
            'patient_id' => $patient->id,
            'doctor_id'  => $request->doctor_id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Review submitted successfully',
            'data'    => $review->load(['doctor.user', 'patient.user'])
        ], Response::HTTP_CREATED);
    }

    /**
     * PUT /user/reviews/{review}
     * تعديل تقييم (المريض صاحب التقييم فقط)
     */
    public function update(Request $request, Review $review)
    {
        $patient = Patient::where('user_id', $request->user()->id)->first();

        if (!$patient || $review->patient_id !== $patient->id) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized'
            ], Response::HTTP_FORBIDDEN);
        }

        $validator = validator($request->all(), [
            'rating'  => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $review->update($request->only(['rating', 'comment']));

        return response()->json([
            'status'  => true,
            'message' => 'Review updated successfully',
            'data'    => $review->load(['doctor.user'])
        ], Response::HTTP_OK);
    }

    /**
     * DELETE /user/reviews/{review}  OR  /admin/reviews/{review}
     * حذف تقييم (المريض أو الأدمن)
     */
    public function destroy(Request $request, Review $review)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            $patient = Patient::where('user_id', $user->id)->first();
            if (!$patient || $review->patient_id !== $patient->id) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthorized'
                ], Response::HTTP_FORBIDDEN);
            }
        }

        $review->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Review deleted successfully'
        ], Response::HTTP_OK);
    }
}
