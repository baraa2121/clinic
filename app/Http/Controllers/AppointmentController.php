<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $query = Appointment::with(['doctor.user', 'patient.user'])->latest();

        if ($user->role === 'doctor' && $user->doctor) {
            $query->where('doctor_id', $user->doctor->id);
        } elseif ($user->role === 'patient' && $user->patient) {
            $query->where('patient_id', $user->patient->id);
        }
        // admin: no filter

        return response()->json([
            'status' => true,
            'data'   => $query->paginate(20),
        ]);
    }

    public function store(Request $request)
    {
        $patient = $request->user()->patient;

        if (!$patient) {
            return response()->json([
                'status'  => false,
                'message' => 'Patient profile not found',
            ], 404);
        }

        $validator = validator($request->all(), [
            'doctor_id'        => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        // Check for duplicate booking
        $exists = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => false,
                'message' => 'This time slot is already booked',
            ], 409);
        }

        $appointment = Appointment::create([
            'patient_id'       => $patient->id,
            'doctor_id'        => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status'           => 'pending',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Appointment booked successfully',
            'data'    => $appointment->load(['doctor.user', 'patient.user']),
        ], 201);
    }

    public function show(Appointment $appointment)
    {
        return response()->json([
            'status' => true,
            'data'   => $appointment->load(['doctor.user', 'patient.user']),
        ]);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validator = validator($request->all(), [
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $appointment->update(['status' => $request->status]);

        return response()->json([
            'status'  => true,
            'message' => 'Appointment updated successfully',
            'data'    => $appointment,
        ]);
    }

    public function destroy(Appointment $appointment)
    {
        if ($appointment->status === 'completed') {
            return response()->json([
                'status'  => false,
                'message' => 'Completed appointments cannot be cancelled',
            ], 403);
        }

        $appointment->update(['status' => 'cancelled']);

        return response()->json([
            'status'  => true,
            'message' => 'Appointment cancelled successfully',
        ]);
    }
}
