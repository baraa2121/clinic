<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FavoriteController extends Controller
{
    /**
     * GET /user/favorites
     * عرض كل الأطباء المفضلين للمريض الحالي
     */
    public function index(Request $request)
    {
        $patient = Patient::where('user_id', $request->user()->id)->first();

        if (!$patient) {
            return response()->json([
                'status'  => false,
                'message' => 'Patient profile not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $favorites = Favorite::with(['doctor.user', 'doctor.department'])
            ->where('patient_id', $patient->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $favorites
        ], Response::HTTP_OK);
    }

    /**
     * POST /user/favorites
     * إضافة طبيب إلى المفضلة
     */
    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'doctor_id' => 'required|exists:doctors,id',
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

        // تحقق إذا الطبيب موجود مسبقًا في المفضلة
        $exists = Favorite::where('patient_id', $patient->id)
            ->where('doctor_id', $request->doctor_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => false,
                'message' => 'Doctor already in favorites'
            ], Response::HTTP_CONFLICT);
        }

        $favorite = Favorite::create([
            'patient_id' => $patient->id,
            'doctor_id'  => $request->doctor_id,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Doctor added to favorites',
            'data'    => $favorite->load(['doctor.user', 'doctor.department'])
        ], Response::HTTP_CREATED);
    }

    /**
     * DELETE /user/favorites/{doctor_id}
     * حذف طبيب من المفضلة
     */
    public function destroy(Request $request, $doctorId)
    {
        $patient = Patient::where('user_id', $request->user()->id)->first();

        if (!$patient) {
            return response()->json([
                'status'  => false,
                'message' => 'Patient profile not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $favorite = Favorite::where('patient_id', $patient->id)
            ->where('doctor_id', $doctorId)
            ->first();

        if (!$favorite) {
            return response()->json([
                'status'  => false,
                'message' => 'Favorite not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $favorite->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Doctor removed from favorites'
        ], Response::HTTP_OK);
    }

    // ─── Admin Routes ────────────────────────────────────────────────────────

    /**
     * GET /admin/favorites
     * عرض كل المفضلات (للأدمن)
     */
    public function adminIndex()
    {
        $favorites = Favorite::with(['doctor.user', 'patient.user'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'status' => true,
            'data'   => $favorites
        ], Response::HTTP_OK);
    }
}
