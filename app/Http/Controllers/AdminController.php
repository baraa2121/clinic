<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::where('role', 'admin')->get();

        return response()->json([
            'status' => true,
            'data' => $admins
        ], Response::HTTP_OK);
    }

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

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->getMessageBag()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        $admin = new User();
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = Hash::make($request->password);
        $admin->role = 'admin';
        $admin->save();

        return response()->json([
            'status' => true,
            'message' => 'Admin created successfully',
            'data' => $admin
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $admin = User::where('role', 'admin')->find($id);

        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Admin not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => true,
            'data' => $admin
        ], Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $admin = User::where('role', 'admin')->find($id);

        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Admin not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email,' . $admin->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->getMessageBag()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        $admin->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Admin updated successfully',
            'data' => $admin
        ], Response::HTTP_OK);
    }
 

    public function destroy($id)
    {
        $admin = User::where('role', 'admin')->find($id);

        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Admin not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $admin->delete();

        return response()->json([
            'status' => true,
            'message' => 'Admin deleted successfully'
        ], Response::HTTP_OK);
    }
}
