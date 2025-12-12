<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApartmentRequest;
use App\Models\Apartment;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    public function createNewApartment(StoreApartmentRequest $request)
    {
 $apartmentFields = $request->validated();
$apartmentFields['user_id'] = $request->user()->id;
if($request->hasFile('photoOfApartment'))
{
$path = $request->file('photoOfApartment')->store('photoOfApartment','public');
$apartmentFields['photoOfApartment'] = $path;

}
// فصل العلاقات
$apartmentData = $apartmentFields;
unset($apartmentFields['amenities'], $apartmentFields['phones']);

// إنشاء الشقة
$apartment = Apartment::create($apartmentFields);

// ربط المميزات
if(!empty($apartmentData['amenities'])){
    $apartment->amenities()->sync($apartmentData['amenities']);
}

// إضافة الهواتف
if(!empty($apartmentData['phones'])){
    foreach($apartmentData['phones'] as $phone){
        $apartment->phones()->create(['phone_number' => $phone]);
    }
}

// تحميل العلاقات
$apartment->load('amenities','phones','city.province');

return response()->json([
    'message' => 'Apartment created successfully',
    'price'=> $apartment->price.$apartment->price_unit,
    'title' => $apartment->title,
    'province' => $apartment->city->province->name,
    'city' => $apartment->city->name,
    'amenities' => $apartment->amenities->pluck('name'),
    'phones' => $apartment->phones->pluck('phone_number')
], 201);
}
public function showAllApartments()
{
    $apartments = Apartment::with('amenities','phones','city.province')->get();
    $result = $apartments->map(function($apartment) {
        return [
            'price'=> $apartment->price.$apartment->price_unit,
            'title' => $apartment->title,
            'province' => $apartment->city->province->name,
            'city' => $apartment->city->name,
            'amenities' => $apartment->amenities->pluck('name'),
            'phones' => $apartment->phones->pluck('phone_number')
        ];
    });
    return response()->json($result);
}
public function searchByFilters(Request $request)
{
    if($request->has('provinces'))
    {
        $province = Province::where('name',$request->input('provinces'))->first();
        $province = Province::where('name', $request->input('provinces'))->first();

if (!$province) {
    return response()->json(['message' => 'Province not found'], 404);
}

$apartments = Apartment::whereHas('city', function($q) use ($province) {
        $q->where('province_id', $province->id);
    })
    ->with('amenities', 'phones', 'city.province')
    ->get();$province = Province::where('name', $request->input('provinces'))->first();

if (!$province) {
    return response()->json(['message' => 'Province not found'], 404);
}

$apartments = Apartment::whereHas('city', function($q) use ($province) {
        $q->where('province_id', $province->id);
    })
    ->with('amenities', 'phones', 'city.province')
    ->get();
        $result = $apartments->map(function($apartment) {
            return [
                'price'=> $apartment->price.$apartment->price_unit,
                'title' => $apartment->title,
                'province' => $apartment->city->province->name,
                'city' => $apartment->city->name,
                'amenities' => $apartment->amenities->pluck('name'),
                'phones' => $apartment->phones->pluck('phone_number'),
                'status'=>'pending'
            ];
        });
        return response()->json($result);
    }
if($request->has('city'))
{
    $city = City::where('name',$request->input('city'))->first();
    $apartments = $city->apartments()->with('amenities','phones','city.province')->get();
    $result = $apartments->map(function($apartment) {
        return [
            'price'=> $apartment->price.$apartment->price_unit,
            'title' => $apartment->title,
            'province' => $apartment->city->province->name,
            'city' => $apartment->city->name,
            'amenities' => $apartment->amenities->pluck('name'),
            'phones' => $apartment->phones->pluck('phone_number')
        ];
    });
    return response()->json($result);
}
if($request->has('price'))
{
    $price = $request->input('price');


$range = 50000;
$min = $price * 0.9;
$max = $price * 1.1;
$apartments = Apartment::whereBetween('price', [$min, $max])
    ->with('amenities','phones','city.province')
    ->get();
    $result = $apartments->map(function($apartment) {
        return [
            'price'=> $apartment->price.$apartment->price_unit,
            'title' => $apartment->title,
            'province' => $apartment->city->province->name,
            'city' => $apartment->city->name,
            'amenities' => $apartment->amenities->pluck('name'),
            'phones' => $apartment->phones->pluck('phone_number')
        ];
    });
    return response()->json($result);
}
if($request->has('area'))
{
   $area = $request->input('area');
   $range = 50;
   $maxArea = $area+$range;
   $minArea = $area - $range;
    $apartments = Apartment::whereBetween('area',[$minArea,$maxArea])->with('amenities','phones','city.province')->get();
    $result = $apartments->map(function($apartment) {
        return [
            'price'=> $apartment->price.$apartment->price_unit,
            'title' => $apartment->title,
            'province' => $apartment->city->province->name,
            'city' => $apartment->city->name,
            'amenities' => $apartment->amenities->pluck('name'),
            'phones' => $apartment->phones->pluck('phone_number')
        ];
    });
    return response()->json($result);
}
}
}
