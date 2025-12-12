<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterNewUserRequest extends FormRequest
{
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
{
    throw new \Illuminate\Http\Exceptions\HttpResponseException(
        response()->json([
            'errors' => $validator->errors(),
        ], 422)
    );
}
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
        return
        [
           'phone' => 'required|unique:users,phone|string|regex:/^\+?[0-9]+$/|min:10|max:13',
           'password'=>'required|string|min:8|confirmed',
           'role'=>'string|required|max:255',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'dateOfBirth' => 'required|date_format:d/m/Y',
            'personalPhoto' => 'required|image|max:2048|mimes:png,jpg,jpeg,gif',
            'IDPhoto' => 'required|image|max:2048|mimes:png,jpg,jpeg,gif'
        ];
    }
    public function prepareForValidation()
{
$clean = $this->phone;
        if (strpos($clean, '09') === 0) {
            $clean = '+963' . substr($clean, 1);
        }
        $this->merge([
            'phone' => $clean,
        ]);
    }
    public function messages(): array
    {
        return [
          'phone.required'=>'Phone number is required',
          'phone.unique'=>'Phone number is already registered',
          'phone.string'=>'Phone number must be a string',
        'phone.regex'=>'Phone number must contain only digits',
            'phone.min'=>'Phone number must be at least 10 digits',
            'phone.max'=>'Phone number must not exceed 10 digits',
          'password.required'=>'Password is required',
          'password.min'=>'Password must be at least 8 characters',
          'password.confirmed'=>'Password confirmation does not match',
          'role.required'=>'Role is required',
                   'firstName.required' => 'First name is required.',
                'firstName.string' => 'First name must be a string.',
                'firstName.max' => 'First name may not be greater than 255 characters.',
                'lastName.required' => 'Last name is required.',
                'lastName.string' => 'Last name must be a string.',
                'lastName.max' => 'Last name may not be greater than 255 characters.',
                'dateOfBirth.required' => 'Date of birth is required.',
                'personalPhoto.required' => 'Personal photo is required.',
                'personalPhoto.image' => 'Personal photo must be an image file.',
                'IDPhoto.required' => 'ID photo is required.',
                'IDPhoto.image' => 'ID photo must be an image file.'
        ];
    }
}
