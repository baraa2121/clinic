<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    //
    /*
    *Authuntcation Methods
    1-Login
    2-Logout
    3-ForgotPassword
    4-Reset Password
    5- Change Password
    6- Update profile
    7-Verify Email
    */
    //     function Login(Request $request,string $guard)
    // {
    //     $validator = Validator($request->all(), [
    //         'email' => 'required|email|exists:admins,email',
    //         'password' => 'required|String',
    //         'remember' => 'required|boolean',
    //     ]);
    //     if (!$validator->fails()) {
           
    //         if (Auth::guard(session('guard'))->attempt($request->only(['email', 'password']), $request->input('remember'))) {
    //             return response()->json([
    //                 'status' => true,
    //                 'message' => 'Logged in succsessfully',
    //                 Response::HTTP_OK
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Wrong email or password',
    //                 Response::HTTP_BAD_REQUEST
    //             ]);
    //         }
    //     } else {
    //         return response()->json(
    //             ['status' => false, 'message' => $validator->getMessageBag()->first()],
    //             Response::HTTP_BAD_REQUEST
    //         );
    //     }
    // }
public function login(Request $request)
{
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:exists:users,email',
            'password' => 'required|string',
        ]);

    $response = Http::asForm()->post('http://127.0.0.1:8001/oauth/token', [
        'grant_type' => 'password',
        'client_id' => env('USER_CLIENT_ID'),
        'client_secret' => env('USER_CLIENT_SECRET'),
        'username' => $request->email,
        'password' => $request->password,
        'scope' => '*',
    ]);

    $json = $response->json();

    if (!isset($json['access_token'])) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    return response()->json([
        'status' => true,
        'access_token' => $json['access_token'],
        'token_type' => $json['token_type'],
        'expires_in' => $json['expires_in'],
        'user'=>$user = User::where('email', $request->email)->first()
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
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
      // مهم جدًا
    ]);

    // =========================
    // Create Patient profile
    // =========================
    Patient::create([
        'user_id' => $user->id,
        'national_id' => null,
        'date_of_birth' => null,
        'address' => null,
    ]);

    // =========================
    // Create API Token (Sanctum)
    // =========================

    return response()->json([
        'status' => true,
        'message' => 'Registered successfully',
        'user' => $user,
    ], 201);
}


    function showLogin(Request $request,string $guard)
    {

        return response()->view('cms.pages.auth.login', compact('guard'));
    }

    function editPassword(Request $request)
    {
        // Add edit password form logic here if needed
    }

    function updatePassword(Request $request)
    {
        $validator = Validator($request->all(), [
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)
                ->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
        ]);

        if (!$validator->fails()) {
            $user = $request->user(session('guard'));
            $user->password = Hash::make($request->input('new_password'));
            $saved = $user->save();

            return response()->json(
                ['status' => $saved, 'message' => $saved ? "Password Updated Successfully" : "Password Update Failed!"],
                $saved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
            );
        }

        return response()->json(
            ['status' => false, 'message' => $validator->getMessageBag()->first()],
            Response::HTTP_BAD_REQUEST
        );
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
        $validator = Validator($request->all(), [
            'password' => 'required|string|current_password:user-api',
            'new_password' => [
                'required', 'confirmed', 'string',
                Password::min(3)
                    ->letters()
                    ->symbols()
                    ->mixedCase()
                    ->uncompromised()
            ]
        ]);

        if (!$validator->fails()) {
            $request->user()->forceFill([
                'password' => Hash::make($request->input('new_password')),
            ]);
            $request->user()->save();

            return response()->json(
                ['status' => true, 'message' => 'Password changed successfully'],
                Response::HTTP_OK
            );
        }

        return response()->json(
            ['status' => false, 'message' => $validator->getMessageBag()->first()],
            Response::HTTP_BAD_REQUEST
        );
    }

    protected function getBroker()
    {
        $guard = session('guard', config('auth.defaults.guard'));

        return $guard === 'admin' ? 'admins' : 'users';
    }

    function requestPasswordReset(Request $request)   
     {
    $validator = Validator($request->all(), [
        'email' => 'required|email',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->getMessageBag()->first()
        ], 400);
    }

    $status = Password::broker($this->getBroker())
        ->sendResetLink($request->only('email'));

    return response()->json([
        'status' => $status == Password::RESET_LINK_SENT,
        'message' => __($status)
    ], $status == Password::RESET_LINK_SENT ? 200 : 400);
}
    function logout(Request $request)
    {
        $guard = session('guard');
        auth($guard)->logout();
        $request->session()->invalidate();

        return redirect()->route('login', $guard ?? 'admin');
    }
}
