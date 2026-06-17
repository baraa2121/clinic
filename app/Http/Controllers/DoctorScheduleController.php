<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DoctorScheduleController extends Controller
{
    /**
     * Display a listing of schedules.
     */
    public function index()
    {
        $data = Schedule::with('doctor')->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $data
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created schedule.
     */
    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'doctor_id'   => 'required|exists:doctors,id',
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time'  => 'required',
            'end_time'    => 'required|after:start_time',
        ]);

        if (!$validator->fails()) {

            $schedule = new Schedule();
            $schedule->doctor_id = $request->input('doctor_id');
            $schedule->day_of_week = $request->input('day_of_week');
            $schedule->start_time = $request->input('start_time');
            $schedule->end_time = $request->input('end_time');

            $saved = $schedule->save();

            return response()->json([
                'status' => $saved,
                'message' => $saved ? "Created Successfully" : "Create Failed!",
                'data' => $schedule
            ], $saved ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST);

        } else {
            return response()->json([
                'status' => false,
                'message' => $validator->getMessageBag()->first()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update schedule
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validator = validator($request->all(), [
            'doctor_id'   => 'required|exists:doctors,id',
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time'  => 'required',
            'end_time'    => 'required|after:start_time',
        ]);

        if (!$validator->fails()) {

            $schedule->doctor_id = $request->input('doctor_id');
            $schedule->day_of_week = $request->input('day_of_week');
            $schedule->start_time = $request->input('start_time');
            $schedule->end_time = $request->input('end_time');

            $saved = $schedule->save();

            return response()->json([
                'status' => $saved,
                'message' => $saved ? "Updated Successfully" : "Update Failed!",
                'data' => $schedule
            ], $saved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);

        } else {
            return response()->json([
                'status' => false,
                'message' => $validator->getMessageBag()->first()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Delete schedule
     */
    public function destroy(string $id)
    {
        $deleted = Schedule::destroy($id);

        return response()->json([
            'status' => $deleted,
            'message' => $deleted ? "Deleted Successfully" : "Delete Failed!"
        ], $deleted ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }
}