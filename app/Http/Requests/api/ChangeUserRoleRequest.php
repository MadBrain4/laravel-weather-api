<?php

namespace App\Http\Requests\api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChangeUserRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'role' => [
                'required',
                function ($attribute, $value, $fail) {
                    $assignable = config('roles.assignable');
                    $lowerAssignable = array_map('strtolower', $assignable);

                    if (!in_array(strtolower($value), $lowerAssignable)) {
                        $fail(__('validation.role_invalid'));
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => __('validation.role_required'),
            'role.in' => __('validation.role_invalid'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $validator->errors(),
        ], 422)); 
    }
}
