<?php

namespace App\Http\Controllers;

use App\Models\Slot;
use App\Models\Service;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SlotController extends Controller
{
    /**
     * GET /doctor/services/{service}/slots  OR  /user/services/{service}/slots
     * عرض كل السلوتات لخدمة معينة
     */
    public function index(Request $request, Service $service)
    {
        $user = $request->user();

        $query = Slot::where('service_id', $service->id);

        // المريض يشوف المتاحة فقط
        if ($user->role === 'patient') {
            $query->where('is_available', true)
                ->where('date', '>=', now()->toDateString());
        }

        $slots = $query->orderBy('date')->orderBy('start_time')->get();

        return response()->json([
            'status' => true,
            'data'   => $slots
        ], Response::HTTP_OK);
    }

    /**
     * GET /slots/{slot}
     * عرض سلوت واحد
     */
    public function show(Slot $slot)
    {
        return response()->json([
            'status' => true,
            'data'   => $slot->load(['service.doctor.user'])
        ], Response::HTTP_OK);
    }

    /**
     * POST /doctor/services/{service}/slots
     * إضافة سلوت جديد للدكتور
     */
    public function store(Request $request, Service $service)
    {
        $user = $request->user();

        // تحقق أن الدكتور يضيف سلوت لخدمته هو
        if ($user->role === 'doctor') {
            $doctor = Doctor::where('user_id', $user->id)->first();
            if (!$doctor || $service->doctor_id !== $doctor->id) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthorized: This service does not belong to you'
                ], Response::HTTP_FORBIDDEN);
            }
        }

        $validator = validator($request->all(), [
            'date'       => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // تحقق من عدم تداخل السلوتات
        $overlap = Slot::where('service_id', $service->id)
            ->where('date', $request->date)
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                  ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('start_time', '<=', $request->start_time)
                         ->where('end_time', '>=', $request->end_time);
                  });
            })->exists();

        if ($overlap) {
            return response()->json([
                'status'  => false,
                'message' => 'This slot overlaps with an existing slot'
            ], Response::HTTP_CONFLICT);
        }

        $slot = Slot::create([
            'service_id'   => $service->id,
            'date'         => $request->date,
            'start_time'   => $request->start_time,
            'end_time'     => $request->end_time,
            'is_available' => true,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Slot created successfully',
            'data'    => $slot
        ], Response::HTTP_CREATED);
    }

    /**
     * PUT /doctor/slots/{slot}  OR  /admin/slots/{slot}
     * تعديل سلوت
     */
    public function update(Request $request, Slot $slot)
    {
        $user = $request->user();

        if ($user->role === 'doctor') {
            $doctor = Doctor::where('user_id', $user->id)->first();
            if (!$doctor || $slot->service->doctor_id !== $doctor->id) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthorized'
                ], Response::HTTP_FORBIDDEN);
            }
        }

        $validator = validator($request->all(), [
            'date'         => 'sometimes|date|after_or_equal:today',
            'start_time'   => 'sometimes|date_format:H:i',
            'end_time'     => 'sometimes|date_format:H:i|after:start_time',
            'is_available' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $slot->update($request->only(['date', 'start_time', 'end_time', 'is_available']));

        return response()->json([
            'status'  => true,
            'message' => 'Slot updated successfully',
            'data'    => $slot
        ], Response::HTTP_OK);
    }

    /**
     * DELETE /doctor/slots/{slot}  OR  /admin/slots/{slot}
     * حذف سلوت
     */
    public function destroy(Request $request, Slot $slot)
    {
        $user = $request->user();

        if ($user->role === 'doctor') {
            $doctor = Doctor::where('user_id', $user->id)->first();
            if (!$doctor || $slot->service->doctor_id !== $doctor->id) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthorized'
                ], Response::HTTP_FORBIDDEN);
            }
        }

        $slot->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Slot deleted successfully'
        ], Response::HTTP_OK);
    }
}
