<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use App\Models\Apartment;
use App\Models\Booking;
use App\Models\Rating;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    use ApiResponse;

    public function addRating(Request $request)
    {
        $validate = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'rating'       => 'required|in:1,2,3,4,5',
            'comment'      => 'nullable|string|max:1000',
        ]);

        $user = $request->user();

        $booking = Booking::where('user_id', $user->id)
            ->where('apartment_id', $validate['apartment_id'])
            ->orderBy('end_date', 'desc')
            ->first();

        if (!$booking) {
            return $this->errorResponse('Unauthorized to rate this booking', 403);
        }

        if ($booking->end_date > Carbon::now('UTC')->toDateString()) {
            return $this->errorResponse('Booking not finished', 403);
        }

        if ($booking->status !== 'approved') {
            return $this->errorResponse('Cannot rate a booking that is not approved', 403);
        }

        if ($booking->rating) {
            return $this->errorResponse('Booking already rated', 403);
        }

        $rating = Rating::create([
            'user_id'    => $user->id,
            'booking_id' => $booking->id,
            'rating'     => $validate['rating'],
            'comment'    => $validate['comment'] ?? null,
        ]);

        return $this->successResponse(
            $rating,
            'Rating added successfully',
            201
        );
    }

    public function showAllRatingsToApartment(Request $request)
    {
        $apartment = Apartment::with('routings.user')->findOrFail($request->id);

        $ratings = $apartment->routings->map(function ($rating) {
            return [
                'user'    => $rating->user->profile->firstName ?? 'Anonymous',
                'rating'  => $rating->rating,
                'comment' => $rating->comment,
            ];
        });

        return $this->successResponse(
            $ratings,
            'Apartment ratings'
        );
    }
}
