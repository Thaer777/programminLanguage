<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogInUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone'    => 'required|string|regex:/^\+?[0-9]+$/|min:10|max:13',
            'password' => 'required|string|min:8'
        ];
    }

    public function prepareForValidation()
    {
        $phone = $this->phone;
        if (strpos($phone, '09') === 0) {
            $phone = '+963' . substr($phone, 1);
        }
        $this->merge(['phone' => $phone]);
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Phone number is required',
            'phone.regex'    => 'Phone number must contain only digits',
            'phone.min'      => 'Phone number must be at least 10 digits',
            'phone.max'      => 'Phone number must not exceed 13 digits',
            'password.required' => 'Password is required',
            'password.min'      => 'Password must be at least 8 characters',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation error',
            
            ], 422)
        );
    }
}
