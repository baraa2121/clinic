<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $data = Department::withCount('doctors')->paginate(10);
        if ($request->expectsJson()) {
            return response()->json([
                'status' => true,
                'data' => $data
            ], Response::HTTP_OK);
        }
        return response()->view('cms.pages.departments.read', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {   
        //
        $data =Department::all();
        return response()->view('cms.pages.departments.create',['data'=>$data]);

    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $validator = Validator($request->all(), [
        'name' => 'required|string|max:100|unique:departments,name',
        'description' => 'nullable|string',
    ]);

    if (!$validator->fails()) {

        $department = new Department();

        $department->name = $request->input('name');
        $department->description = $request->input('description');

        $saved = $department->save();

        return response()->json(
            [
                'status' => $saved,
                'message' => $saved
                    ? 'Created Successfully'
                    : 'Create Failed!'
            ],
            $saved
                ? Response::HTTP_CREATED
                : Response::HTTP_BAD_REQUEST
        );
    }

    return response()->json(
        [
            'status' => false,
            'message' => $validator->getMessageBag()->first()
        ],
        Response::HTTP_BAD_REQUEST
    );
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
public function edit(Department $department)
{
    return response()->view(
        'cms.pages.departments.edit',
        ['department' => $department]
    );
}

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, Department $department)
{
    $validator = Validator($request->all(), [
        'name' => 'required|string|max:100|unique:departments,name,' . $department->id,
        'description' => 'nullable|string',
    ]);

    if (!$validator->fails()) {

        $department->name = $request->input('name');
        $department->description = $request->input('description');

        $saved = $department->save();

        return response()->json(
            [
                'status' => $saved,
                'message' => $saved
                    ? 'Updated Successfully'
                    : 'Update Failed!'
            ],
            $saved
                ? Response::HTTP_OK
                : Response::HTTP_BAD_REQUEST
        );
    }

    return response()->json(
        [
            'status' => false,
            'message' => $validator->getMessageBag()->first()
        ],
        Response::HTTP_BAD_REQUEST
    );
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
{
    $deleted = $department->delete();

    return response()->json(
        [
            'status' => $deleted,
            'message' => $deleted
                ? 'Deleted Successfully'
                : 'Delete Failed!'
        ],
        $deleted
            ? Response::HTTP_OK
            : Response::HTTP_BAD_REQUEST
    );
}
}
