<?php

namespace App\Http\Controllers;

use App\Models\Amenitie;
use Illuminate\Http\Request;

class AmenitieController extends Controller
{
public function showAllAmenities()
{
    $amenities = Amenitie::all();
    return response()->json($amenities);
}
}
