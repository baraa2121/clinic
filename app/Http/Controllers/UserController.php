<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = User::paginate(10);

        return response()->json([
            'status' => true,
            'data' => $data
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->mixedCase()
                    ->symbols()
                    ->numbers()
                    ->letters()
                    ->uncompromised()
            ],
        ]);

        if (!$validator->fails()) {

            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->role = 'user';
            $saved = $user->save();


            // Create Patient automatically
            Patient::create([
                'user_id' => $user->id,
            ]);

            return response()->json([
                'status' => $saved,
                'message' => $saved ? "Created Successfully" : "Create Failed!",
                'data' => $user
            ], $saved ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST);
        } else {
            return response()->json([
                'status' => false,
                'message' => $validator->getMessageBag()->first()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email,' . $user->id,
        ]);

        if (!$validator->fails()) {

            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $saved = $user->save();

            return response()->json([
                'status' => $saved,
                'message' => $saved ? "Updated Successfully" : "Update Failed!",
                'data' => $user
            ], $saved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
        } else {
            return response()->json([
                'status' => false,
                'message' => $validator->getMessageBag()->first()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $countOfDestroyed = User::destroy($id);

        return response()->json([
            'status' => $countOfDestroyed,
            'message' => $countOfDestroyed ? "Deleted Successfully" : "Delete Failed!"
        ], $countOfDestroyed ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }
}
