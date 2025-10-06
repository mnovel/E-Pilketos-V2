<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Http\Requests\StoreClassesRequest;
use App\Http\Requests\UpdateClassesRequest;
use App\Http\Resources\ClassResource;
use App\Traits\ApiResponse;

class ClassesController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:admin'])->except(['index']);
    }

    public function index()
    {
        $class = Classes::with('electionSession')->get();
        return $this->successResponse(ClassResource::collection($class), 'Classes retrieved successfully');
    }

    public function show(Classes $class)
    {
        return $this->successResponse(new ClassResource($class), 'Class details retrieved successfully');
    }


    public function store(StoreClassesRequest $request)
    {
        $class = Classes::create($request->validated());
        return $this->successResponse(new ClassResource($class), 'Class created successfully', 201);
    }

    public function update(UpdateClassesRequest $request, Classes $class)
    {
        $class->update($request->validated());
        return $this->successResponse(new ClassResource($class), 'Class updated successfully');
    }

    public function destroy(Classes $class)
    {
        $class->delete();
        return $this->successResponse(null, 'Class deleted successfully');
    }
}
