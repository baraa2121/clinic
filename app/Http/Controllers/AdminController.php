<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Dotenv\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Psy\Util\Str;

class AdminController extends Controller
{

    public function index(Request $request)
    {
        $data = Admin::all();
        if ($request->expectsJson()) {
            return response()->json(['status' => true, 'data' => $data], Response::HTTP_OK);
        }
        return response()->view('cms.pages.admins.read', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return response()->view('cms.pages.admins.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:admins,email',
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
            $admin = new Admin();
            $admin->name = $request->input('name');
            $admin->email = $request->input('email');
            $admin->password = Hash::make($request->input('password'));
            $saved = $admin->save();
            $admin->role = 'admin';

            return response()->json(
                ['status' => $saved, 'message' => $saved ? "Created Successfully" : "Create Failed!"],
                $saved ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST
            );
        } else {
            return response()->json(
                ['status' => false, "message" => $validator->getMessageBag()->first()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //        return response()->view('cms.pages.admins.edit', ['roles' => $roles, 'admin' => $admin]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
            //
        ;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        $validator = Validator($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:admins,email,' . $admin->id,


        ]);

        if (!$validator->fails()) {
            $admin->name = $request->input('name');
            $admin->email = $request->input('email');
            $saved = $admin->save();

            return response()->json(
                ['status' => $saved, 'message' => $saved ? "Updated Successfully" : "Update Failed!"],
                $saved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
            );
        } else {
            return response()->json(
                ['status' => false, "message" => $validator->getMessageBag()->first()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $id)
    {
        //
        $countOfDestroyed = Admin::destroy($id);
        return response()->json(
            ['status' => $countOfDestroyed, 'message' => $countOfDestroyed ? "Deleted Successfully" : "Delete Failed!"],
            $countOfDestroyed ?  Response::HTTP_OK : Response::HTTP_BAD_REQUEST
        );
    }
}
