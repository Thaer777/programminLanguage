<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreApartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'images'   => 'required|array|min:1',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'description' => 'nullable|string',
            'city_id' => 'required|exists:cities,id',
            'district' => 'nullable|string|max:255',
            'price' => 'required|numeric',
            'price_unit' => 'nullable|string|max:50',
            'area' => 'nullable|integer',
            'area_unit' => 'nullable|string|max:50',
            'CategoryOfRentType' => 'nullable|in:family,single,students,employees',
            'rooms_number' => 'nullable|integer',
            'floor' => 'nullable|in:land,first,second,third,fourth,fifth',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
            'phoneOfOwner' => 'nullable|string|max:20',
        ];
    }
}
