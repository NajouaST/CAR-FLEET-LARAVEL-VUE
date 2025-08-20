<?php

namespace App\Http\Controllers\API\user;

use App\Http\Controllers\Controller;
use App\Http\Resources\NameResource;
use App\Notifications\ManagerNotification;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

// Assuming you want to notify users
class RoleController extends Controller
{
    use HasQueryFilters;
    public function listNames()
    {
        $roles = Role::all();

        return NameResource::collection($roles);
    }
    public function index(Request $request)
    {
        $query = Role::query()
            ->withCount('permissions')
            ->with(['permissions' => function ($query) {
                $query->select('permissions.id', 'permissions.name')
                    ->limit(5);
            }]);

        $result = $this->applyQueryParameters($query, $request);

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create([
            'guard_name' => 'web',
            'name' => $validated['name']
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        auth()->user()->notify(new ManagerNotification("Role", $role, "create"));

        return response()->json($role->load('permissions'), 201);
    }

    public function show($id)
    {
        $role = Role::with(['permissions' => function ($query) {
            $query->select('permissions.id', 'permissions.name');
        }])->findOrFail($id);
        return $role;
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->update($validated);
        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        auth()->user()->notify(new ManagerNotification("Role", $role, "update"));

        return response()->json($role->load('permissions'));
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return response()->noContent();
    }
}
