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

    public function getLanguage(Request $request)
    {
        return response()->json([
            'language' => $request->user()->language,
        ]);
    }

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
