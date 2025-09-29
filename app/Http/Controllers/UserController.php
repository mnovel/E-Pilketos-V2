<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsersRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $users = User::where('role', '!=', 'voter')->get();
        return $this->successResponse($users, 'Users retrieved successfully');
    }

    public function show(User $user)
    {
        return $this->successResponse($user, 'User retrieved successfully');
    }

    public function store(StoreUsersRequest $request)
    {
        $user = User::create($request->validated());
        return $this->successResponse($user, 'User created successfully');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());
        return $this->successResponse($user, 'User updated successfully');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'voter') {
            return $this->errorResponse('Voter users cannot be deleted');
        }
        $user->delete();
        return $this->successResponse(null, 'User deleted successfully');
    }
}
