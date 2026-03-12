<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        
        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role ?? 'user'],
        ], 200);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role ?? 'user'],
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logout successful'], 200);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role ?? 'user'],
        ]);
    }
}
