<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    /**
     * عرض كل الإشعارات
     */
    public function index()
    {
        $data = Notification::with(['user', 'appointment'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $data
        ], Response::HTTP_OK);
    }

    /**
     * إنشاء Notification جديد
     */
 

    /**
     * تحديث حالة الإشعار (read/unread)
     */
    public function update(Request $request, Notification $notification)
    {
        $validator = validator($request->all(), [
            'status' => 'required|in:read,unread',
        ]);

        if (!$validator->fails()) {

            $notification->status = $request->input('status');
            $saved = $notification->save();

            return response()->json([
                'status' => $saved,
                'message' => $saved ? "Updated Successfully" : "Update Failed!",
                'data' => $notification
            ], $saved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);

        } else {
            return response()->json([
                'status' => false,
                'message' => $validator->getMessageBag()->first()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * حذف إشعار
     */
    public function destroy(string $id)
    {
        $deleted = Notification::destroy($id);

        return response()->json([
            'status' => $deleted,
            'message' => $deleted ? "Deleted Successfully" : "Delete Failed!"
        ], $deleted ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }
}