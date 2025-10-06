<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegisterRequest;
use App\Http\Resources\ParticipantResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Handle user registration
     */
    public function register(StoreRegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create($request->validated()['user']);
            $participant = $user->participant()->create($request->validated()['participant']);
            $user->status = 'pending';
            $user->save();
            DB::commit();
            return $this->successResponse(new ParticipantResource($participant), 'Participant created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create participant: ' . $e->getMessage());
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
            return $this->errorResponse('Invalid credentials', 401);
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse('Login successful', [
            'user'  => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
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
            return $this->errorResponse('User not found', 404);
        }

        // (Optional) verify token validity here
        $user->update([
            'password' => bcrypt($validated['password']),
        ]);

        return $this->successResponse('Password reset successful');
    }
}
