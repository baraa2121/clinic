<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Support\Facades\Http;
class AuthController extends Controller
{
  
public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => false,
            'message' => $validator->getMessageBag()->first(),
        ], 422);
    }

    if (!Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password])) {
        return response()->json([
            'status'  => false,
            'message' => 'Invalid credentials',
        ], 401);
    }

    $user  = Auth::guard('web')->user();
    $token = $user->createToken('auth-token')->accessToken;

    return response()->json([
        'status'       => true,
        'access_token' => $token,
        'token_type'   => 'Bearer',
        'user'         => $user,
    ]);
}  
  
    public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->getMessageBag()->first(),
        ], 400);
    }

    // =========================
    // Create User
    // =========================
    $user = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
        'role'     => 'patient',
    ]);

    Patient::create([
        'user_id'       => $user->id,
        'national_id'   => null,
        'date_of_birth' => null,
        'address'       => null,
    ]);

    $token = $user->createToken('auth-token')->accessToken;

    return response()->json([
        'status'       => true,
        'message'      => 'Registered successfully',
        'access_token' => $token,
        'token_type'   => 'Bearer',
        'user'         => $user,
    ], 201);
}


    function sendVerificationEmail(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => true,
                'message' => 'Email already verified'
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'status' => true,
            'message' => 'Email verification sent successfully'
        ]);
    }

function changePassword(Request $request)
{
    // 1) تحقق من البيانات (بدون current_password rule)
    $validator = Validator::make($request->all(), [
        'password' => 'required|string', // كلمة المرور الحالية
        'new_password' => [
            'required',
            'confirmed',
            'string',
            Password::min(8)
                ->letters()
                ->symbols()
                ->mixedCase()
                ->uncompromised()
        ]
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->first()
        ], Response::HTTP_BAD_REQUEST);
    }

    $user = $request->user();

    // 2) تحقق يدوي من كلمة المرور الحالية (الأهم)
    if (!Hash::check($request->password, $user->password)) {
        return response()->json([
            'status' => false,
            'message' => 'Current password is incorrect'
        ], Response::HTTP_FORBIDDEN);
    }

    // 3) تحديث كلمة المرور
    $user->update([
        'password' => Hash::make($request->new_password)
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Password changed successfully'
    ], Response::HTTP_OK);
}

  public function requestPasswordReset(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->first()
        ], 400);
    }

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return response()->json([
        'status' => $status === Password::RESET_LINK_SENT,
        'message' => __($status)
    ], $status === Password::RESET_LINK_SENT ? 200 : 400);
     
}

public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();
        }
    );

    return response()->json([
        'status' => $status === Password::PASSWORD_RESET,
        'message' => $status === Password::PASSWORD_RESET
            ? 'Password reset successfully'
            : 'Invalid token or email'
    ], $status === Password::PASSWORD_RESET ? 200 : 400);
}
    
    public function logout(Request $request)
    {
        $user = $request->user('api');
        $revoked = $user->token()->revoke();
        return response()->json(
            [
                'status' => $revoked,
                "message" => $revoked ? 'Logged out successfully' : 'Logged out failed',
                'user' => $revoked
            ],
            $revoked ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
        );
    }
}
