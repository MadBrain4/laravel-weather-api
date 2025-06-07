<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\StoreRoleRequest;
use App\Http\Requests\api\UpdateRoleRequest;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
        /**
     * Crear un nuevo rol
     *
     * Este endpoint permite crear un nuevo rol en el sistema.
     *
     * @authenticated
     *
     * @bodyParam name string required Nombre del rol. Debe ser único. Example: editor
     * @bodyParam guard_name string Nombre del guard (por defecto 'web'). Example: web
     *
     * @response 201 {
     *   "message": "Rol creado exitosamente",
     *   "role": {
     *     "id": 5,
     *     "name": "editor",
     *     "guard_name": "web",
     *     "created_at": "2025-06-07T13:00:00.000000Z",
     *     "updated_at": "2025-06-07T13:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "name": [
     *       "The name has already been taken."
     *     ]
     *   }
     * }
     */
    public function store(StoreRoleRequest $request)
    {
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => $request->get('guard_name', 'web'),
        ]);

        return response()->json([
            'message' => __('role.created_successfully'),
            'role' => $role,
        ], 201);
    }

        /**
     * Actualizar un rol existente
     *
     * Este endpoint permite modificar el nombre o guard de un rol existente.
     *
     * @authenticated
     *
     * @urlParam role int required ID del rol a actualizar. Example: 5
     * @bodyParam name string required Nuevo nombre del rol. Example: administrador
     * @bodyParam guard_name string Nuevo guard (opcional). Example: api
     *
     * @response 200 {
     *   "message": "Rol actualizado exitosamente",
     *   "role": {
     *     "id": 5,
     *     "name": "administrador",
     *     "guard_name": "api",
     *     "created_at": "2025-06-07T13:00:00.000000Z",
     *     "updated_at": "2025-06-07T13:15:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "name": [
     *       "The name must be at least 3 characters."
     *     ]
     *   }
     * }
     */
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
