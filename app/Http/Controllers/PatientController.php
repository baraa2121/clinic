<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    /**
     * عرض بروفايل المريض المسجّل دخوله (للمريض نفسه)
     * GET /api/user/profile
     */
    public function profile(Request $request)
    {
        $patient = $request->user()->patient;

        if (!$patient) {
            return response()->json([
                'status'  => false,
                'message' => 'Patient profile not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => true,
            'data'   => $patient->load('user')
        ], Response::HTTP_OK);
    }

    /**
     * تحديث بروفايل المريض المسجّل دخوله (للمريض نفسه)
     * PUT /api/user/profile
     */
    public function updateProfile(Request $request)
    {
        $patient = $request->user()->patient;

        if (!$patient) {
            return response()->json([
                'status'  => false,
                'message' => 'Patient profile not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'name'          => 'nullable|string|min:3|max:100',
            'phone'         => 'nullable|string|min:7|max:15',
            'date_of_birth' => 'nullable|date',
            'address'       => 'nullable|string|max:255',
            'national_id'   => 'nullable|string|max:15|unique:patients,national_id,' . $patient->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->getMessageBag()->first()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // تحديث جدول users
        $patient->user->update(array_filter([
            'name'  => $request->name,
            'phone' => $request->phone,
        ], fn($v) => !is_null($v)));

        // تحديث جدول patients
        $patient->update(array_filter([
            'date_of_birth' => $request->date_of_birth,
            'address'       => $request->address,
            'national_id'   => $request->national_id,
        ], fn($v) => !is_null($v)));

        return response()->json([
            'status'  => true,
            'message' => 'Profile updated successfully',
            'data'    => $patient->load('user')
        ], Response::HTTP_OK);
    }

    /**
     * عرض بروفايل مريض محدد بالـ ID (للأدمن أو الدكتور)
     * GET /api/admin/patients/{patient}
     * GET /api/doctor/patients/{patient}
     */
    public function show(Patient $patient)
    {
        return response()->json([
            'status' => true,
            'data'   => $patient->load('user')
        ], Response::HTTP_OK);
    }

    /**
     * البحث عن المرضى (للأدمن أو الدكتور)
     * GET /api/admin/patients/search
     * GET /api/doctor/patients/search
     */
    public function searchPatients(Request $request)
    {
        $query = Patient::with('user');

        // البحث بالكلمة المفتاحية (الاسم / الهاتف / رقم الهوية)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->whereHas('user', function ($u) use ($keyword) {
                    $u->where('name', 'like', "%{$keyword}%")
                      ->orWhere('phone', 'like', "%{$keyword}%");
                })
                ->orWhere('national_id', 'like', "%{$keyword}%");
            });
        }

        // فلتر تاريخ الميلاد
        if ($request->filled('date_of_birth')) {
            $query->whereDate('date_of_birth', $request->date_of_birth);
        }

        // فلتر رقم الهوية
        if ($request->filled('national_id')) {
            $query->where('national_id', $request->national_id);
        }

        return response()->json([
            'status' => true,
            'data'   => $query->paginate(10)
        ], Response::HTTP_OK);
    }
}
