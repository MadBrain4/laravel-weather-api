<?php

namespace App\Http\Requests\api;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Foundation\Http\FormRequest;


class WeatherRequest extends FormRequest
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
            'city' => 'required|string|max:100|regex:/^[\pL\s\-]+$/u', // evita símbolos raros
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'city.required' => __('validation.city_required'),
            'city.string'   => __('validation.city_string'),
            'city.max'      => __('validation.city_max'),
            'city.regex'    => __('validation.city_invalid_format'),
        ];
    }

    /**
     * Custom failed validation response
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }
}
