<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        if (!Auth::attempt($validated)) {
            return response()->json([
                'message' => 'Login information invalid'
            ], 401);
        }
        $user = Auth::user();
        if (!$user->status) {
            return response()->json([
                'message' => 'User is not active.'
            ], 403);
        }
        


        $user = User::where('email', $validated['email'])->first();

        return response()->json([
            'user' => $user,
            'access_token' => $user->createToken('api_token')->plainTextToken,
            'token_type' => 'Bearer',
        ]);

    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        // Ensure the password is hashed before storing
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json($user, 201);
    }
}
