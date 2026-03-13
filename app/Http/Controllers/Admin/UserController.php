<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::orderBy('name')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'nullable|in:user,agent,admin',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'user',
        ]);
        return response()->json($user->makeHidden('password'), 201);
    }


    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'role' => 'sometimes|required|in:user,agent,admin',
        ]);
        $user->update($request->only(['name', 'email', 'role']));
        
        return response()->json($user);
    }
    
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
    public function indexDeleted()
    {
        $deletedUsers = User::query()
        ->onlyTrashed()->orderBy('name')->get();
        return response()->json($deletedUsers, 200);
    }
    public function userRestore($id)
    {
        User::query()->where('id', $id)->restore();
        return response()->json(['message' => 'User restored successfully'], 200);
    }
}
