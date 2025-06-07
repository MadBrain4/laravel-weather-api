<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\StoreRoleRequest;
use App\Http\Requests\api\UpdateRoleRequest;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function store(StoreRoleRequest $request)
    {
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => $request->get('guard_name', 'web'),
        ]);

        return response()->json([
            'message' => _('role.created_successfully'),
            'role' => $role,
        ], 201);
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update([
            'name' => $request->input('name'),
            'guard_name' => $request->input('guard_name', $role->guard_name),
        ]);

        return response()->json([
            'message' => __('role.updated_successfully'),
            'role' => $role,
        ]);
    }
}
