<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    /**
     * GET /admin/payments
     * عرض كل المدفوعات (الأدمن)
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            $payments = Payment::with(['appointment.doctor.user', 'patient.user'])
                ->latest()
                ->paginate(15);
        } else {
            // المريض يشوف مدفوعاته فقط
            $patient = Patient::where('user_id', $user->id)->first();
            $payments = Payment::with(['appointment.doctor.user'])
                ->where('patient_id', optional($patient)->id)
                ->latest()
                ->paginate(15);
        }

        return response()->json([
            'status' => true,
            'data'   => $payments
        ], Response::HTTP_OK);
    }

    /**
     * GET /admin/payments/{payment}  OR  /user/payments/{payment}
     * عرض تفاصيل مدفوعة واحدة
     */
    public function show(Request $request, Payment $payment)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            $patient = Patient::where('user_id', $user->id)->first();
            if (!$patient || $payment->patient_id !== $patient->id) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthorized'
                ], Response::HTTP_FORBIDDEN);
            }
        }

        return response()->json([
            'status' => true,
            'data'   => $payment->load(['appointment.doctor.user', 'patient.user'])
        ], Response::HTTP_OK);
    }

    /**
     * POST /user/payments
     * إنشاء دفعة جديدة مرتبطة بموعد
     */
    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'appointment_id' => 'required|exists:appointments,id',
            'amount'         => 'required|numeric|min:0',
            'method'             => 'required|in:cash,card,online',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $patient = Patient::where('user_id', $request->user()->id)->first();

        if (!$patient) {
            return response()->json([
                'status'  => false,
                'message' => 'Patient profile not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // تحقق أن الموعد يخص المريض
        $appointment = Appointment::where('id', $request->appointment_id)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$appointment) {
            return response()->json([
                'status'  => false,
                'message' => 'Appointment not found or does not belong to you'
            ], Response::HTTP_NOT_FOUND);
        }

        // تحقق أنه لم يتم الدفع مسبقاً
        $alreadyPaid = Payment::where('appointment_id', $appointment->id)->exists();
        if ($alreadyPaid) {
            return response()->json([
                'status'  => false,
                'message' => 'This appointment has already been paid'
            ], Response::HTTP_CONFLICT);
        }

        $payment = Payment::create([
            'appointment_id' => $appointment->id,
            'patient_id'     => $patient->id,
            'amount'         => $request->amount,
            'method'         => $request->method,
            'status'         => 'paid',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Payment recorded successfully',
            'data'    => $payment->load(['appointment.doctor.user'])
        ], Response::HTTP_CREATED);
    }

    /**
     * PUT /admin/payments/{payment}
     * تحديث حالة الدفعة (الأدمن فقط)
     */
    public function update(Request $request, Payment $payment)
    {
        $validator = validator($request->all(), [
            'status' => 'required|in:paid,pending,refunded,failed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $payment->update(['status' => $request->status]);

        return response()->json([
            'status'  => true,
            'message' => 'Payment status updated',
            'data'    => $payment
        ], Response::HTTP_OK);
    }

    /**
     * DELETE /admin/payments/{payment}
     * حذف دفعة (الأدمن فقط)
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Payment deleted successfully'
        ], Response::HTTP_OK);
    }
}
