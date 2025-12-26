<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApartmentRequest;
use App\Models\Apartment;
use App\Models\Province;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    use ApiResponse;

public function createNewApartment(StoreApartmentRequest $request)
{
    $apartmentFields = $request->validated();
    $apartmentFields['user_id'] = $request->user()->id;
    $apartmentFields['phoneOfOwner'] = $request->user()->phone;

    // نخزن البيانات بدون amenities و images
    $apartmentData = $apartmentFields;
    unset($apartmentFields['amenities'], $apartmentFields['images']);

    // إنشاء الشقة
    $apartment = Apartment::create($apartmentFields);

    // ربط الميزات
    if (!empty($apartmentData['amenities'])) {
        $apartment->amenities()->sync($apartmentData['amenities']);
    }

    // حفظ الصور (متعددة)
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('apartments', 'public');
            $apartment->images()->create([
                'image_path' => $path
            ]);
        }
    }

    $apartment->load('amenities', 'city.province', 'images');

    return $this->successResponse([
        'title'        => $apartment->title,
        'price'        => $apartment->price . $apartment->price_unit,
        'province'     => $apartment->city->province->name,
        'city'         => $apartment->city->name,
        'amenities'    => $apartment->amenities->pluck('name'),
        'phoneOfOwner' => $apartment->phoneOfOwner,
        'images'       => $apartment->images->map(
            fn ($img) => asset('storage/' . $img->image_path)
        ),
        'area'         => $apartment->area . $apartment->area_unit,
        'CategoryOfRentType' => $apartment->CategoryOfRentType,
        'rooms_number' => $apartment->rooms_number,
        'floor'        => $apartment->floor,
    ], 'Apartment created successfully', 201);
}

    /* =======================
       SHOW ALL APARTMENTS
    ======================= */
  public function showAllApartments()
{
    $apartments = Apartment::with('amenities', 'city.province', 'images')->get();

    $result = $apartments->map(function ($apartment) {
        return [
            'title'        => $apartment->title,
            'price'        => $apartment->price . $apartment->price_unit,
            'province'     => $apartment->city->province->name,
            'city'         => $apartment->city->name,
            'amenities'    => $apartment->amenities->pluck('name'),
            'phones'       => $apartment->phoneOfOwner,
            'images'       => $apartment->images->map(
                fn ($img) => asset('storage/' . $img->image_path)
            ),
            'area'         => $apartment->area . $apartment->area_unit,
            'CategoryOfRentType' => $apartment->CategoryOfRentType,
            'rooms_number' => $apartment->rooms_number,
            'floor'        => $apartment->floor,
        ];
    });

    return $this->successResponse($result, 'Apartments fetched successfully');
}

    /* =======================
       SEARCH BY FILTERS
    ======================= */
    public function searchByFilters(Request $request)
    {
$query = Apartment::where('status', 'approved');

        if ($request->filled('province')) {
            $province = Province::where('name', $request->province)->first();

            if (!$province) {
                return $this->errorResponse('Province not found', 404);
            }

            $query->whereHas('city', function ($q) use ($province) {
                $q->where('province_id', $province->id);
            });
        }

        if ($request->filled('city')) {
            $query->whereHas('city', function ($q) use ($request) {
                $q->where('name', $request->city);
            });
        }

        if ($request->filled('price')) {
            $min = $request->price * 0.9;
            $max = $request->price * 1.1;
            $query->whereBetween('price', [$min, $max]);
        }

        if ($request->filled('area')) {
            $query->whereBetween('area', [
                $request->area - 50,
                $request->area + 50
            ]);
        }

        $apartments = $query->with('amenities', 'city.province')->get();

        $result = $apartments->map(function ($apartment) {
            return [
                'title'     => $apartment->title,
                'price'     => $apartment->price . $apartment->price_unit,
                'area'      => $apartment->area,
                'province'  => $apartment->city->province->name,
                'city'      => $apartment->city->name,
                'amenities' => $apartment->amenities->pluck('name'),
                'phones'    => $apartment->phoneOfOwner,
            ];
        });

        return $this->successResponse($result, 'Filtered apartments');
    }

    /* =======================
       GET APARTMENT BY ID
    ======================= */
    public function getApartmentById(Request $request)
    {
        $apartment = Apartment::find($request->id);

        if (!$apartment) {
            return $this->errorResponse('Apartment not found', 404);
        }

        return $this->successResponse([
            'title'            => $apartment->title,
            'price'            => $apartment->price . $apartment->price_unit,
            'description'      => $apartment->description,
            'province'         => $apartment->city->province->name,
            'city'             => $apartment->city->name,
            'amenities'        => $apartment->amenities->pluck('name'),
            'phones'           => $apartment->phoneOfOwner,
            'photoOfApartment' => asset('storage/' . $apartment->photoOfApartment),
            'area'             => $apartment->area . $apartment->area_unit,
            'CategoryOfRentType' => $apartment->CategoryOfRentType,
            'rooms_number'     => $apartment->rooms_number,
            'floor'            => $apartment->floor,
        ], 'Apartment fetched successfully');
    }
}
