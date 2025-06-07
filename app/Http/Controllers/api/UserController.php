<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\ChangeUserRoleRequest;
use App\Http\Requests\api\UpdateLanguageRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
        /**
     * Actualizar idioma del usuario
     *
     * Este endpoint permite cambiar el idioma preferido del usuario autenticado.
     *
     * @authenticated
     *
     * @bodyParam language string required Idioma a establecer. Debe ser uno de los valores soportados: en, es, fr, etc. Example: es
     *
     * @response 200 {
     *   "message": "Idioma actualizado correctamente",
     *   "language": "es"
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "language": [
     *       "The selected language is invalid."
     *     ]
     *   }
     * }
     */
    public function updateLanguage(UpdateLanguageRequest  $request)
    {
        $request->validate([
            'language' => 'required|in:' . implode(',', config('languages.supported')),
        ]);

        $user = $request->user();
        $user->language = $request->language;
        $user->save();

        app()->setLocale($user->language);

        return response()->json([
            'message' => __('auth.language_updated'),
            'language' => $user->language,
        ]);
    }

        /**
     * Obtener idioma actual del usuario
     *
     * Devuelve el idioma configurado actualmente para el usuario autenticado.
     *
     * @authenticated
     *
     * @response 200 {
     *   "language": "es"
     * }
     */

    public function getLanguage(Request $request)
    {
        return response()->json([
            'language' => $request->user()->language,
        ]);
    }

        /**
     * Cambiar rol de un usuario
     *
     * Este endpoint permite cambiar el rol de un usuario específico, si el usuario autenticado tiene permisos para hacerlo.
     *
     * @authenticated
     *
     * @urlParam user int required ID del usuario cuyo rol se desea cambiar. Example: 2
     *
     * @bodyParam role string required Rol a asignar. Debe ser uno de los roles asignables. Example: admin
     *
     * @response 200 {
     *   "message": "Rol actualizado correctamente",
     *   "user": {
     *     "id": 2,
     *     "name": "Pedro Sánchez",
     *     "email": "pedro@example.com",
     *     "roles": ["admin"]
     *   }
     * }
     *
     * @response 403 {
     *   "message": "No se puede cambiar el rol del superadmin"
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "role": [
     *       "The selected role is invalid."
     *     ]
     *   }
     * }
     */
    public function changeUserRole(ChangeUserRoleRequest $request, User $user)
    {
        $validRoles = config('roles.roles');
        $assignableRoles = config('roles.assignable');

        $normalizedRole = strtolower($request->role);
        $user->syncRoles([$normalizedRole]);

        if ($user->hasRole('superadmin')) {
            return response()->json([
                'message' => __('auth.cannot_change_superadmin_role')
            ], 403);
        }

        $user->syncRoles([$request->role]);

        return response()->json([
            'message' => __('auth.role_updated'),
            'user' => $user,
        ]);
    }

}
