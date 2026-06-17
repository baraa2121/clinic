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

class DoctorController extends Controller
{
    // GET /api/doctors
    public function index()
    {
        $data = Doctor::with(['user', 'department'])
            ->withCount(['appointments'])
            ->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $data
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'department_id' => 'required|exists:departments,id',
            'password' => ['required', 'string', Password::min(8)],
            'experience_years' => 'required|string',
            'specialization' => 'required|string',
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | Create User
            |--------------------------------------------------------------------------
            */
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role = 'doctor';
            $user->save();

            /*
            |--------------------------------------------------------------------------
            | Create Doctor
            |--------------------------------------------------------------------------
            */
            $doctor = new Doctor();
            $doctor->user_id = $user->id;
            $doctor->department_id = $request->department_id;

            // Image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/doctors'), $imageName);

                $doctor->image = 'images/doctors/' . $imageName;
            }

            $doctor->experience_years = $request->experience_years;
            $doctor->specialization = $request->specialization;
            $doctor->address = $request->address;

            $doctor->save();

            return response()->json([
                'status' => true,
                'message' => 'Doctor Created Successfully',
                'data' => $doctor->load(['user', 'department'])
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
    public function update(Request $request, Doctor $doctor)
    {
        $validator = Validator($request->all(), [
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $doctor->user_id,
            'department_id' => 'required|exists:departments,id',
            'experience_years' => 'required|string',
            'specialization' => 'required|string',
            'address' => 'required|string',
        ]);

        if (!$validator->fails()) {

            // Update User
            $user = $doctor->user;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $savedUser = $user->save();

            // Image
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/doctors'), $imageName);

                $doctor->image = 'images/doctors/' . $imageName;
            }

            // Update Doctor
            $doctor->department_id = $request->input('department_id');
            $doctor->experience_years = $request->input('experience_years');
            $doctor->specialization = $request->input('specialization');
            $doctor->address = $request->input('address');

            $savedDoctor = $doctor->save();

            $status = $savedUser && $savedDoctor;

            return response()->json(
                [
                    'status' => $status,
                    'message' => $status ? "Updated Successfully" : "Update Failed!"
                ],
                $status ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
            );
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => $validator->getMessageBag()->first()
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
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
            ->paginate(12);
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
