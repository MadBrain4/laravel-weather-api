<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\LoginRequest;
use App\Http\Requests\api\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;

class AuthController extends Controller
{
    /**
    * Registro de nuevo usuario
    *
    * Este endpoint permite registrar un nuevo usuario en el sistema.
    *
    * @unauthenticated
    *
    * @bodyParam name string required Nombre completo del usuario. Example: Juan Pérez
    * @bodyParam email string required Correo electrónico único. Example: juan@example.com
    * @bodyParam password string required Contraseña segura. Debe coincidir con password_confirmation. Example: secret123
    * @bodyParam password_confirmation string required Confirmación de la contraseña. Debe ser igual a password. Example: secret123
    *
    * @response 201 {
    *   "message": "Usuario registrado correctamente",
    *   "user": {
    *     "id": 1,
    *     "name": "Juan Pérez",
    *     "email": "juan@example.com",
    *     "updated_at": "2025-06-07T12:00:00.000000Z",
    *     "created_at": "2025-06-07T12:00:00.000000Z"
    *   },
    *   "token": "1|xXxXxXxXxXxXxXxXxXxXxXx"
    * }
    *
    * @response 422 {
    *   "message": "The given data was invalid.",
    *   "errors": {
    *     "email": [
    *       "The email has already been taken."
    *     ],
    *     "password": [
    *       "The password confirmation does not match."
    *     ]
    *   }
    * }
    */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        
        $user->assignRole('user');

        return response()->json([
            'message' => __('auth.register_success'),
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken,
        ], 201);
    }

        /**
     * Iniciar sesión
     *
     * Este endpoint permite autenticar a un usuario existente.
     *
     * @unauthenticated
     *
     * @bodyParam email string required Correo electrónico del usuario. Example: juan@example.com
     * @bodyParam password string required Contraseña del usuario. Example: secret123
     *
     * @response 200 {
     *   "message": "Inicio de sesión exitoso",
     *   "user": {
     *     "id": 1,
     *     "name": "Juan Pérez",
     *     "email": "juan@example.com",
     *     "created_at": "2025-06-07T12:00:00.000000Z",
     *     "updated_at": "2025-06-07T12:00:00.000000Z"
     *   },
     *   "token": "1|xXxXxXxXxXxXxXxXxXxXxXx"
     * }
     *
     * @response 401 {
     *   "message": "Credenciales inválidas"
     * }
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => __('auth.invalid_credentials')], 401);
        }

        App::setLocale($user->language ?? config('app.locale'));

        return response()->json([
            'message' => __('auth.login_success'),
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken,
        ]);
    }

        /**
     * Obtener usuario autenticado
     *
     * Devuelve la información del usuario autenticado mediante el token.
     *
     * @authenticated
     *
     * @response 200 {
     *   "id": 1,
     *   "name": "Juan Pérez",
     *   "email": "juan@example.com",
     *   "created_at": "2025-06-07T12:00:00.000000Z",
     *   "updated_at": "2025-06-07T12:00:00.000000Z"
     * }
     */

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

        /**
     * Cerrar sesión
     *
     * Este endpoint invalida el token actual del usuario autenticado.
     *
     * @authenticated
     *
     * @response 200 {
     *   "message": "Sesión cerrada correctamente"
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => __('auth.logout_success')]);
    }
}
