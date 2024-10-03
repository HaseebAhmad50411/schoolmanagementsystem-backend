<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

use function Laravel\Prompts\password;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user->is_admin) {
            return response()->json([
                'message' => 'User is not Admin.'
            ], 403);
        }
        $search = $request['search'] ?? '';
        $perpage = $request['perpage'] ?? 10000;
        $cols = [
            'id',
           'name',
           'email',
           'password',
           'status'
        ];

            $users = User::where(function ($query) use ($search, $cols) {
                foreach ($cols as $col) {
                    $query->orWhere($col, 'LIKE', "%{$search}%");
                }
            })->paginate($perpage);

            return response()->json($users, 200);


    }

    public function store(StoreUserRequest $request)
    {

        $validated = $request->validated();
        $user = User::create($validated);
        return response()->json($user,201);

    }

    public function show(User $user)
    {
        $user = auth()->user();

        if (!$user->is_admin) {
            return response()->json([
                'message' => 'User is not Admin.'
            ], 403);
        }
        return new UserResource($user);

    }

    public function update(UpdateUserRequest $request, User $user)
    {
        if (!auth()->user()->is_admin) {
            return response()->json([
                'message' => 'User is not Admin.'
            ], 403);
        }
        $validated = $request->validated();

        $user->update($validated);

        return response()->json($user);

    }

    public function destroy(User $user)
    {

        $user->delete();
        return response()->json(null, 204);
    }
}
