<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;

class ApiAuthController extends Controller
{
    //
  
      function verificationNotice(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => true,
            'message' => $user->hasVerifiedEmail()
                ? 'Email already verified'
                : 'Please verify your email address'
        ]);
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
    public function logout(Request $request)
    {
        $user = $request->user('user-api');
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
    public function changePassword(Request $request) {}
    public function updateProfile(Request $request)
    {
        $validator = Validator($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email,' . $request->user('user-api')->id,

        ]);

        if (!$validator->fails()) {
            $user = $request->user('user-api');
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $saved = $user->save();
            return response()->json(
                ['status' => $saved, 'message' => $saved ? "Profile Updated Successfully" : "Update Failed!"],
                $saved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
            );
        } else {
            return response()->json(
                ['status' => false, "message" => $validator->getMessageBag()->first()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}


//Userid->019eb0d7-65a9-712c-b91c-b98846ea0ecd
//UserClient->7dtQxMpxNTFj8XkwLq67kMfSd1B5aNGNSQDxGEmI

//AdminId->019eb0d9-ec63-73f1-a847-7a936992374d
//AdminClient->indHlOge4izx2WFpQ1zVjMwnlnud5QxHaFjPViJM