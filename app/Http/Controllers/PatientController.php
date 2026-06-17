<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
class PatientController extends Controller
{
    //

public function profile(Patient $patient)
{
    return response()->json([
        'status' => true,
        'data' => $patient->load('user')
    ], Response::HTTP_OK);
}

public function updateProfile(Request $request, Patient $patient)
{
    $validator = Validator::make($request->all(), [
        'name' => 'nullable|string|min:3',
        'phone' => 'nullable|string|min:7|max:15',
        'date_of_birth' => 'nullable|date',
        'address' => 'nullable|string|max:255',
        'national_id' => 'nullable|string|max:15|unique:patients,national_id,' . $patient->id,
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->getMessageBag()->first()
        ], 400);
    }

    // تحديث users
    $patient->user->update(array_filter([
        'name' => $request->name,
        'phone' => $request->phone,
    ]));

    // تحديث patients
    $patient->update(array_filter([
        'date_of_birth' => $request->date_of_birth,
        'address' => $request->address,
        'national_id' => $request->national_id,
    ]));

    return response()->json([
        'status' => true,
        'message' => 'Profile updated successfully',
        'data' => $patient->load('user')
    ]);
}

public function searchPatients(Request $request)
{
    $query = Patient::with('user');

    // 🔎 Keyword (name / phone / national_id)
    if ($request->filled('keyword')) {
        $keyword = $request->keyword;

        $query->where(function ($q) use ($keyword) {
            
            // البحث في جدول users
            $q->whereHas('user', function ($user) use ($keyword) {
                $user->where('name', 'like', "%{$keyword}%")
                     ->orWhere('phone', 'like', "%{$keyword}%");
            })

            // 🔑 البحث في رقم الهوية (patients table)
            ->orWhere('national_id', 'like', "%{$keyword}%");
        });
    }

    // 🎂 Date of birth filter
    if ($request->filled('date_of_birth')) {
        $query->whereDate('date_of_birth', $request->date_of_birth);
    }

    // 🔑 Search by exact national_id (optional dedicated filter)
    if ($request->filled('national_id')) {
        $query->where('national_id', $request->national_id);
    }

    return response()->json([
        'status' => true,
        'data' => $query->paginate(10)
    ]);
}

}
