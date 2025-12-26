<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterNewUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /* =======================
       RULES
    ======================= */
    public function rules(): array
    {
        return [
            'phone'         => 'required|string|regex:/^\+?[0-9]+$/|min:10|max:13|unique:users,phone',
            'password'      => 'required|string|min:8|confirmed',
            'role'          => 'required|string|in:owner,renter',
            'firstName'     => 'required|string|max:255',
            'lastName'      => 'required|string|max:255',
            'dateOfBirth'   => 'required|date_format:d/m/Y',
            'personalPhoto' => 'required|image|max:2048|mimes:png,jpg,jpeg,gif',
            'IDPhoto'       => 'required|image|max:2048|mimes:png,jpg,jpeg,gif',
        ];
    }

    /* =======================
       PREPARE DATA
    ======================= */
    protected function prepareForValidation()
    {
        $phone = $this->phone;

        if ($phone && str_starts_with($phone, '09')) {
            $phone = '+963' . substr($phone, 1);
        }

        $this->merge([
            'phone' => $phone,
        ]);
    }

    /* =======================
       CUSTOM VALIDATION RESPONSE
    ======================= */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation error',
                
            ], 422)
        );
    }

    /* =======================
       CUSTOM MESSAGES
    ======================= */
    public function messages(): array
    {
        return [
            'phone.required'    => 'Phone number is required',
            'phone.unique'      => 'Phone number is already registered',
            'phone.regex'       => 'Phone number must contain only digits',
            'phone.min'         => 'Phone number must be at least 10 digits',
            'phone.max'         => 'Phone number must not exceed 13 digits',

            'password.required'  => 'Password is required',
            'password.min'       => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',

            'role.required' => 'Role is required',
            'role.in'       => 'Role must be owner or renter',

            'firstName.required' => 'First name is required',
            'lastName.required'  => 'Last name is required',

            'dateOfBirth.required'    => 'Date of birth is required',
            'dateOfBirth.date_format' => 'Date format must be d/m/Y',

            'personalPhoto.required' => 'Personal photo is required',
            'personalPhoto.image'    => 'Personal photo must be an image',

            'IDPhoto.required' => 'ID photo is required',
            'IDPhoto.image'    => 'ID photo must be an image',
        ];
    }
}
