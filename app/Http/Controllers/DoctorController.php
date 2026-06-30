<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;
    use Illuminate\Support\Facades\Auth;
class DoctorController extends Controller
{
    public function dashboard(Request $request)
    {
        $doctor = $request->user()->doctor;
        $base   = \App\Models\Appointment::where('doctor_id', $doctor->id);

        return response()->json([
            'status' => true,
            'data'   => [
                'earnings'           => (float) (clone $base)->where('status', 'completed')->sum('fee'),
                'appointments'       => (clone $base)->count(),
                'patients'           => (clone $base)->distinct('patient_id')->count('patient_id'),
                'latestAppointments' => (clone $base)->with(['patient.user'])->latest()->take(5)->get(),
            ],
        ]);
    }

    // GET /api/doctors
    public function index()
    {
        $data = Doctor::with(['user', 'department'])
            ->withCount(['appointments'])
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $data,
        ], Response::HTTP_OK);
    }
    public function show(Doctor $doctor)
    {
        return response()->json([
            'status' => true,
            'data' => $doctor->load(['user', 'department'])
        ]);
    }

    // GET /api/departments (بديل create view)


    // POST /api/doctors
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'image' => 'nullable|image|mimes:jpg,jpeg,png',
    //         'name' => 'required|string|max:100',
    //         'email' => 'required|email|unique:users,email',
    //         'department_id' => 'required|exists:departments,id',
    //         'password' => ['required', 'string', Password::min(8)],
    //         'experience_years' => 'required|string',
    //         'specialization' => 'required|string',
    //         'address' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $validator->errors()->first(),
    //             'errors' => $validator->errors()
    //         ], Response::HTTP_BAD_REQUEST);
    //     }

    //     try {

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Create User
    //         |--------------------------------------------------------------------------
    //         */
    //         $user = new User();
    //         $user->name = $request->name;
    //         $user->email = $request->email;
    //         $user->password = Hash::make($request->password);
    //         $user->role = 'doctor';
    //         $user->save();

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Create Doctor
    //         |--------------------------------------------------------------------------
    //         */
    //         $doctor = new Doctor();
    //         $doctor->user_id = $user->id;
    //         $doctor->department_id = $request->department_id;

    //         // Image upload
    //         if ($request->hasFile('image')) {
    //             $image = $request->file('image');
    //             $imageName = time() . '_' . $image->getClientOriginalName();
    //             $image->move(public_path('images/doctors'), $imageName);

    //             $doctor->image = 'images/doctors/' . $imageName;
    //         }

    //         $doctor->experience_years = $request->experience_years;
    //         $doctor->specialization = $request->specialization;
    //         $doctor->address = $request->address;

    //         $doctor->save();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Doctor Created Successfully',
    //             'data' => $doctor->load(['user', 'department'])
    //         ], Response::HTTP_CREATED);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong',
    //             'error' => $e->getMessage()
    //         ], Response::HTTP_BAD_REQUEST);
    //     }
    // }
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        'name'        => 'required|string|max:100',
        'email'       => 'required|email|unique:users,email',
        'password'    => ['required', 'string', 'min:8'],
        'speciality'  => 'nullable|string',
        'specialization' => 'nullable|string',
        'experience'  => 'nullable|string',
        'experience_years' => 'nullable|string',
        'fees'        => 'nullable|numeric',
        'consultation_fee' => 'nullable|numeric',
        'about'       => 'nullable|string',
        'bio'         => 'nullable|string',
        'address'     => 'nullable|string',
        'department_id' => 'nullable|exists:departments,id',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
        ], 400);
    }

    try {
        $user = new User();
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->password = Hash::make($request->password);
        $user->role     = 'doctor';
        $user->save();

        $specialization = $request->speciality ?? $request->specialization ?? '';

        // Resolve department_id from speciality name if not provided directly
        $departmentId = $request->department_id;
        if (!$departmentId && $specialization) {
            $dept = Department::where('name', $specialization)->first();
            if (!$dept) {
                $dept = Department::create(['name' => $specialization, 'description' => $specialization]);
            }
            $departmentId = $dept->id;
        }
        if (!$departmentId) {
            $departmentId = Department::first()->id;
        }

        $doctor = new Doctor();
        $doctor->user_id          = $user->id;
        $doctor->department_id    = $departmentId;
        $doctor->specialization   = $specialization;
        $expRaw = $request->experience ?? $request->experience_years ?? 0;
        $doctor->experience_years = (int) preg_replace('/[^0-9]/', '', (string) $expRaw) ?: 0;
        $doctor->consultation_fee = $request->fees ?? $request->consultation_fee ?? 0;
        $doctor->bio              = $request->about ?? $request->bio ?? '';
        $doctor->is_approved      = 1;

        // Handle address (may come as JSON string from frontend)
        if ($request->filled('address')) {
            $addr = $request->address;
            $decoded = json_decode($addr, true);
            $doctor->address = is_array($decoded)
                ? ($decoded['line1'] ?? '') . ($decoded['line2'] ? ', ' . $decoded['line2'] : '')
                : $addr;
        }

        if ($request->hasFile('image')) {
            $image     = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/doctors'), $imageName);
            $doctor->image = 'images/doctors/' . $imageName;
        }

        $doctor->save();

        return response()->json([
            'status'  => true,
            'message' => 'Doctor Created Successfully',
            'data'    => $doctor->load(['user', 'department'])
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
    public function update(Request $request, Doctor $doctor)
    {
        // Toggle availability only
        if ($request->has('is_approved') && count($request->all()) === 1) {
            $doctor->is_approved = (bool) $request->is_approved;
            $doctor->save();
            return response()->json([
                'status'  => true,
                'message' => 'Availability updated',
                'data'    => $doctor->load(['user', 'department']),
            ]);
        }

        $validator = Validator($request->all(), [
            'image'            => 'nullable|image|mimes:jpg,jpeg,png',
            'name'             => 'nullable|string|max:100',
            'email'            => 'nullable|email|unique:users,email,' . $doctor->user_id,
            'department_id'    => 'nullable|exists:departments,id',
            'experience_years' => 'nullable|string',
            'specialization'   => 'nullable|string',
            'address'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->getMessageBag()->first(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $doctor->user;
        if ($request->filled('name'))  $user->name  = $request->name;
        if ($request->filled('email')) $user->email = $request->email;
        $user->save();

        if ($request->hasFile('image')) {
            $image     = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/doctors'), $imageName);
            $doctor->image = 'images/doctors/' . $imageName;
        }

        if ($request->filled('department_id'))    $doctor->department_id    = $request->department_id;
        if ($request->filled('experience_years')) $doctor->experience_years = $request->experience_years;
        if ($request->filled('specialization'))   $doctor->specialization   = $request->specialization;
        if ($request->filled('address'))          $doctor->address          = $request->address;
        if ($request->has('is_approved'))         $doctor->is_approved      = (bool) $request->is_approved;

        $doctor->save();

        return response()->json([
            'status'  => true,
            'message' => 'Updated Successfully',
            'data'    => $doctor->load(['user', 'department']),
        ]);
    }

    /**
     * عرض بروفايل الدكتور المسجّل دخوله (للدكتور نفسه)
     * GET /api/doctor/profile
     */
    public function profile(Request $request)
    {
        $doctor = $request->user()->doctor;

        if (!$doctor) {
            return response()->json([
                'status'  => false,
                'message' => 'Doctor profile not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => true,
            'data'   => $doctor->load(['user', 'department'])
        ], Response::HTTP_OK);
    }

    /**
     * تحديث بروفايل الدكتور المسجّل دخوله (للدكتور نفسه)
     * PUT /api/doctor/profile
     */
    public function updateProfile(Request $request)
    {
        $doctor = $request->user()->doctor;

        if (!$doctor) {
            return response()->json([
                'status'  => false,
                'message' => 'Doctor profile not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'image'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'name'             => 'nullable|string|max:100',
            'email'            => 'nullable|email|unique:users,email,' . $doctor->user_id,
            'department_id'    => 'nullable|exists:departments,id',
            'experience_years' => 'nullable|string',
            'specialization'   => 'nullable|string',
            'address'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // تحديث بيانات user
        $user = $doctor->user;
        if ($request->filled('name'))  $user->name  = $request->name;
        if ($request->filled('email')) $user->email = $request->email;
        $user->save();

        // تحديث الصورة
        if ($request->hasFile('image')) {
            $image     = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/doctors'), $imageName);
            $doctor->image = 'images/doctors/' . $imageName;
        }

        // تحديث بيانات الدكتور
        if ($request->filled('department_id'))    $doctor->department_id    = $request->department_id;
        if ($request->filled('experience_years')) $doctor->experience_years = $request->experience_years;
        if ($request->filled('specialization'))   $doctor->specialization   = $request->specialization;
        if ($request->filled('address'))          $doctor->address          = $request->address;

        $doctor->save();

        return response()->json([
            'status'  => true,
            'message' => 'Profile updated successfully',
            'data'    => $doctor->load(['user', 'department'])
        ], Response::HTTP_OK);
    }

    /**
     * Search Doctors
     */
    public function search(Request $request)
    {
        $query = Doctor::query();

        if ($request->filled('keyword')) {

            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {

                $q->where('doctor_name', 'like', "%{$keyword}%")
                    ->orWhere('specialization', 'like', "%{$keyword}%")
                    ->orWhere('bio', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('specialty')) {
            $query->where('specialization', $request->specialty);
        }

        if ($request->filled('min_price')) {
            $query->where('fee', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('fee', '<=', $request->max_price);
        }

        return $query
            ->latest()
            ->paginate(10);
    }

    /**
     * Top Doctors
     */
    public function topDoctors()
    {
        return Doctor::query()
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->take(10)
            ->get();
    }


    /**
     * Nearby Doctors
     */
}
