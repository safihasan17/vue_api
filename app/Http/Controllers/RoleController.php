<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /** Plain array (same shape as before) so the existing role dropdowns keep working. */
    public function index()
    {
        return response()->json(Role::withCount('users')->orderBy('id')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:2|max:100|unique:roles,name',
        ]);

        $role = Role::create($data);

        return response()->json(['success' => 'Role created successfully', 'role' => $role]);
    }

    public function show(string $id)
    {
        $role = Role::withCount('users')->with('users:id,name,email,role_id')->find($id);

        if (!$role) {
            return response()->json(['error' => true, 'message' => 'Role not found'], 404);
        }

        return response()->json(['success' => true, 'role' => $role, 'protected' => $role->isProtected()]);
    }

    public function update(Request $request, string $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['error' => true, 'message' => 'Role not found'], 404);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100', Rule::unique('roles', 'name')->ignore($role->id)],
        ]);

        $role->update($data);

        return response()->json(['success' => true, 'message' => 'Role updated successfully']);
    }

    public function destroy(string $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['error' => true, 'message' => 'Role not found'], 404);
        }

        if ($role->isProtected()) {
            return response()->json([
                'error'   => true,
                'message' => "\"{$role->name}\" is a core role used by the system and can't be deleted.",
            ], 409);
        }

        if ($role->users()->exists()) {
            return response()->json([
                'error'   => true,
                'message' => "\"{$role->name}\" is assigned to users. Move those users to another role first.",
            ], 409);
        }

        $role->delete();

        return response()->json(['success' => true, 'message' => 'Role deleted successfully']);
    }
}
