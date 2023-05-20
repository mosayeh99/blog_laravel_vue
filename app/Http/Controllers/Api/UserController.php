<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(2);
        return response()->json($users);
    }

    public function store(StoreRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 2
        ]);
        return response()->json($user, 201);
    }

    public function update(UpdateRequest $request, User $user)
    {
        $user = User::find($user->id);

        if ($request->password !== null) {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        } else {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        }
        return response()->json($user);
    }

    public function changeRole(Request $request, User $user)
    {
        $user->update([
            'role' => $request->role,
        ]);
        return response()->noContent();
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->noContent();
    }
}
