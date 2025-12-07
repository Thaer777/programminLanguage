<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'city_id' => 'required|exists:cities,id',
            'street'=> 'nullable|string|max:255',
            'district'=> 'nullable|string|max:255',
            'price'=> 'required|numeric',
            'price_unit'=> 'nullable|string|max:50',
            'area'=> 'nullable|integer',
            'area_unit'=> 'nullable|string|max:50',
            'categoryOfPropertyTpe'=> 'nullable|in:apartment,house,studio,villa,penthouse,duplex',
            'CategoryOfRentType'=> 'nullable|in:family,single,students,employees',
            'rooms_number'=> 'nullable|integer',
            'bathrooms_number'=> 'nullable|integer',
            'living_rooms_number'=> 'nullable|integer',
            'floor'=> 'nullable|in:land,first,second,third,fourth,fifth',
            'ageOfBuilding'=> 'nullable|in:new,1-5 years,6-10 years,11-20 years,old',
            'street_width'=> 'nullable|integer',
            'street_width_unit'=> 'nullable|string|max:50',
            'purpose'=> 'nullable|in:residential,commercial',
            'rental_status'=> 'nullable|boolean',
            'amenities'=> 'nullable|array',
            'amenities.*'=> 'exists:amenities,id',
            'phones'=> 'nullable|array',
            'phones.*'=> 'string|max:20',
        ];

    }
}
