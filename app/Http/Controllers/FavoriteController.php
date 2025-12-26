<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{

    use ApiResponse;

    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|exists:apartments,id'
        ]);

        $user = $request->user();
        $apartmentId = $request->apartment_id;

        if ($user->favorites()->where('apartment_id', $apartmentId)->exists()) {
            $user->favorites()->detach($apartmentId);

            return $this->successResponse(
                null,
                'Apartment removed from favorites'
            );
        }

        $user->favorites()->attach($apartmentId);

        return $this->successResponse(
            null,
            'Apartment added to favorites',
            201
        );
    }

    public function myFavorites(Request $request)
    {
        $favorites = $request->user()
            ->favorites()
            ->with('amenities', 'city.province', 'images')
            ->get();

        $result = $favorites->map(function ($apartment) {
            return [
                'title'    => $apartment->title,
                'price'    => $apartment->price . $apartment->price_unit,
                'province' => $apartment->city->province->name,
                'city'     => $apartment->city->name,
                'area'     => $apartment->area . $apartment->area_unit,
                'images'   => $apartment->images->map(
                    fn ($img) => asset('storage/' . $img->image_path)
                ),
                'amenities' => $apartment->amenities->pluck('name'),
            ];
        });

        return $this->successResponse(
            $result,
            'Favorites fetched successfully'
        );
    }

    public function isFavorite(Request $request, $apartmentId)
    {
        $isFavorite = $request->user()
            ->favorites()
            ->where('apartment_id', $apartmentId)
            ->exists();

        return $this->successResponse([
            'is_favorite' => $isFavorite
        ]);
    }
}

