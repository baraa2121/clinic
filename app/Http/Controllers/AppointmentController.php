<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AppointmentController extends Controller
{
    /**
     * GET all appointments
     */
    public function index()
    {
        $appointments = Appointment::with(['doctor', 'patient'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $appointments
        ], Response::HTTP_OK);
    }

    /**
     * CREATE appointment (Booking)
     */
    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'doctor_id'  => 'required|exists:doctors,id',
            'date'       => 'required|date',
            'time'       => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $doctorId = $request->doctor_id;
        $date     = $request->date;
        $time     = $request->time;

        $dayOfWeek = date('w', strtotime($date)); // 0 = Sunday

        /**
         * 🧠 1. Check if doctor works this day
         */
        $schedule = Schedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$schedule) {
            return response()->json([
                'status' => false,
                'message' => 'Doctor is not available on this day'
            ], 422);
        }

        /**
         * 🧠 2. Check if time is inside working hours
         */
        if ($time < $schedule->start_time || $time > $schedule->end_time) {
            return response()->json([
                'status' => false,
                'message' => 'Selected time is outside doctor working hours'
            ], 422);
        }

        /**
         * 🧠 3. Check overlapping appointments
         */
        $exists = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->where('appointment_time', $time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'This time slot is already booked'
            ], 409);
        }

        /**
         * 🧠 4. Create appointment
         */
        $appointment = Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id'  => $doctorId,
            'appointment_date' => $date,
            'appointment_time' => $time,
            'status' => 'pending'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Appointment created successfully',
            'data' => $appointment
        ], 201);
    }

    /**
     * SHOW appointment
     */
    public function show(Appointment $appointment)
    {
        return response()->json([
            'status' => true,
            'data' => $appointment->load(['doctor', 'patient'])
        ], Response::HTTP_OK);
    }

    /**
     * UPDATE status (Doctor actions)
     */
    public function update(Request $request, Appointment $appointment)
    {
        $validator = validator($request->all(), [
            'status' => 'required|in:pending,confirmed,completed,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $appointment->update([
            'status' => $request->status
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Appointment updated successfully',
            'data' => $appointment
        ], 200);
    }

    /**
     * CANCEL appointment (soft logic)
     */
    public function destroy(Appointment $appointment)
    {
        if (in_array($appointment->status, ['completed'])) {
            return response()->json([
                'status' => false,
                'message' => 'Completed appointments cannot be cancelled'
            ], 403);
        }

        $appointment->update([
            'status' => 'cancelled'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Appointment cancelled successfully'
        ], Response::HTTP_OK);
    }
}