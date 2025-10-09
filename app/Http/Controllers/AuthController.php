<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegisterRequest;
use App\Http\Resources\ParticipantResource;
use App\Http\Resources\UserResource;
use App\Models\Classes;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['logout', 'me']);
    }

    /**
     * Handle user registration
     */
    public function register(StoreRegisterRequest $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            $class = Classes::findOrFail($validated['participant']['class_id']);

            if ($class->participants()->count() >= $class->max_users) {
                return $this->errorResponse(null, 'Class is full');
            }

            $user = User::create($validated['user']);

            $user->update(['status' => 'pending']);

            $participant = $user->participant()->create($validated['participant']);

            DB::commit();

            return $this->successResponse(
                new ParticipantResource($participant),
                'Participant created successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 'Failed to create participant');
        }
    }

    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return $this->errorResponse(null, 'Invalid credentials');
        }

        if ($user->status === 'inactive') {
            return $this->errorResponse(null, 'User account is not active');
        }

        if ($user->role === 'admin') {
            $user->tokens()->delete();
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user'  => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Login successful',);
    }

    /**
     * Handle user logout (revoke token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse('Logout successful');
    }

    /**
     * Get current authenticated user
     */
    public function me(Request $request)
    {
        $user = Auth::user();
        return $this->successResponse(new UserResource($user), 'Authenticated user data retrieved');
    }

    /**
     * Handle password reset
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
            'token'    => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return $this->errorResponse(null, 'User not found');
        }

        // (Optional) verify token validity here
        $user->update([
            'password' => bcrypt($validated['password']),
        ]);

        return $this->successResponse('Password reset successful');
    }
}
