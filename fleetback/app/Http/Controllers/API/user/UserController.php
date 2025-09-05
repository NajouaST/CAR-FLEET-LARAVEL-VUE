<?php

namespace App\Http\Controllers\API\user;

use App\Http\Controllers\Controller;
use App\Http\Resources\NameResource;
use App\Models\User;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use HasQueryFilters;

    public function listNames()
    {
        return User::select('id','name','email')->get();
    }

    public function index(Request $request)
    {
        $authUser = Auth::user();

        if ($authUser->can('users view (all)')) {
            $query = User::query();
        } elseif ($authUser->can('users view (own)')) {
            $query = User::query()->where('id', $authUser->id);
        } else {
            return response()->json(['message' => 'No Access'], 403);
        }

        if ($request->has('deleted_at__dateAfter')) {
            $query->withTrashed();
        }

        $query->withCount('roles')
            ->with(['roles' => function ($query) {
                $query->select('roles.id', 'roles.name')->limit(5);
            }]);

        $result = $this->applyQueryParameters($query, $request);

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'is_active' => 'boolean',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt("12345678"),
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $user->syncRoles($validated['roles'] ?? []);

        return response()->json($user->load('roles'), 201);
    }

    public function show(User $user)
    {
        $authUser = Auth::user();

        if ($authUser->can('users view (all)')) {
            return $user->load('roles');
        } elseif ($authUser->can('users view (own)') && $authUser->id === $user->id) {
            return $user->load('roles');
        } else {
            return response()->json(['message' => 'No Access'], 403);
        }
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'is_active' => 'boolean',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user->update([
            'name' => $validated['name'] ?? $user->name,
            'email' => $validated['email'] ?? $user->email,
            'is_active' => $validated['is_active'] ?? $user->is_active,
        ]);

        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        return response()->json($user->load('roles'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->noContent();
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($user->trashed()) {
            $user->restore();
            return response()->json(['message' => 'User restored successfully.', 'user' => $user->load('roles')]);
        }

        return response()->json(['message' => 'User is not deleted.']);
    }

    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($user->trashed()) {
            $user->forceDelete();
            return response()->json(['message' => 'User permanently deleted.']);
        }

        return response()->json(['message' => 'User is not deleted.']);
    }
}
