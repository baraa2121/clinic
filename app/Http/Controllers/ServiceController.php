<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceController extends Controller
{
    /**
     * GET /doctor/services  OR  /user/services  OR  /admin/services
     * عرض كل الخدمات
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'doctor') {
            // الدكتور يشوف خدماته فقط
            $doctor = Doctor::where('user_id', $user->id)->first();
            $services = Service::with(['slots'])
                ->where('doctor_id', optional($doctor)->id)
                ->latest()
                ->get();
        } else {
            $services = Service::with(['doctor.user', 'slots'])
                ->latest()
                ->paginate(15);
        }

        return response()->json([
            'status' => true,
            'data'   => $services
        ], Response::HTTP_OK);
    }

    /**
     * GET /user/doctors/{doctor}/services  OR  /doctor/doctors/{doctor}/services
     * عرض خدمات طبيب معين
     */
    public function doctorServices($doctorId)
    {
        $services = Service::with(['slots' => fn($q) => $q->where('is_available', true)])
            ->where('doctor_id', $doctorId)
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $services
        ], Response::HTTP_OK);
    }

    /**
     * GET /services/{service}
     * عرض خدمة واحدة
     */
    public function show(Service $service)
    {
        return response()->json([
            'status' => true,
            'data'   => $service->load(['doctor.user', 'slots'])
        ], Response::HTTP_OK);
    }

    /**
     * POST /doctor/services  OR  /admin/services
     * إنشاء خدمة جديدة
     */
    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'doctor_id' => 'sometimes|exists:doctors,id',
            'name'      => 'required|string|max:255',
            'duration'  => 'required|integer|min:1',
            'price'     => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $request->user();

        // إذا الدكتور يضيف خدمة بنفسه
        if ($user->role === 'doctor') {
            $doctor = Doctor::where('user_id', $user->id)->first();
            if (!$doctor) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Doctor profile not found'
                ], Response::HTTP_NOT_FOUND);
            }
            $doctorId = $doctor->id;
        } else {
            // الأدمن يحدد doctor_id
            if (!$request->doctor_id) {
                return response()->json([
                    'status'  => false,
                    'message' => 'doctor_id is required'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $doctorId = $request->doctor_id;
        }

        $service = Service::create([
            'doctor_id' => $doctorId,
            'name'      => $request->name,
            'duration'  => $request->duration,
            'price'     => $request->price,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Service created successfully',
            'data'    => $service->load(['doctor.user'])
        ], Response::HTTP_CREATED);
    }

    /**
     * PUT /doctor/services/{service}  OR  /admin/services/{service}
     * تعديل خدمة
     */
    public function update(Request $request, Service $service)
    {
        $user = $request->user();

        // تحقق أن الدكتور يعدل خدمته هو فقط
        if ($user->role === 'doctor') {
            $doctor = Doctor::where('user_id', $user->id)->first();
            if (!$doctor || $service->doctor_id !== $doctor->id) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthorized'
                ], Response::HTTP_FORBIDDEN);
            }
        }

        $validator = validator($request->all(), [
            'name'     => 'sometimes|string|max:255',
            'duration' => 'sometimes|integer|min:1',
            'price'    => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $service->update($request->only(['name', 'duration', 'price']));

        return response()->json([
            'status'  => true,
            'message' => 'Service updated successfully',
            'data'    => $service->load(['doctor.user'])
        ], Response::HTTP_OK);
    }

    /**
     * DELETE /doctor/services/{service}  OR  /admin/services/{service}
     * حذف خدمة
     */
    public function destroy(Request $request, Service $service)
    {
        $user = $request->user();

        if ($user->role === 'doctor') {
            $doctor = Doctor::where('user_id', $user->id)->first();
            if (!$doctor || $service->doctor_id !== $doctor->id) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthorized'
                ], Response::HTTP_FORBIDDEN);
            }
        }

        $service->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Service deleted successfully'
        ], Response::HTTP_OK);
    }
}
