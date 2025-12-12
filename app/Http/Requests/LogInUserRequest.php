<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogInUserRequest extends FormRequest
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
        return [
              'phone'=>'required|string|regex:/^\+?[0-9]+$/|min:10|max:13',
              'password'=>'required|string|min:8'
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
        // public function messages(): array
        // {
        //     return [
        //       'phone.required'=>'Phone number is required',
        //       'phone.string'=>'Phone number must be a string',
        //       'phone.regex'=>'Phone number must contain only digits',
        //         'phone.min'=>'Phone number must be at least 10 digits',
        //         'phone.max'=>'Phone number must not exceed 10 digits',
        //       'password.required'=>'Password is required',
        //       'password.min'=>'Password must be at least 8 characters',
        //     ];
        // }
    }

