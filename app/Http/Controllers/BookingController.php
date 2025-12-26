<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use App\Models\Apartment;
use App\Models\Booking;
use App\Models\User;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class BookingController extends Controller
{
    use ApiResponse;

    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function storeBookingApartment(Request $request)
    {
        $start_date = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $end_date   = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');

        $request->merge([
            'start_date' => $start_date,
            'end_date'   => $end_date
        ]);

        $validateData = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after:start_date',
        ]);

        if ($request->user()->role !== 'renter') {
            return $this->errorResponse('Only renters can make bookings', 403);
        }

        if ($this->bookingService->hasConflict(
            $request->apartment_id,
            $request->start_date,
            $request->end_date
        )) {
            return $this->errorResponse('This apartment is already booked in this period', 409);
        }

        $validateData['user_id'] = $request->user()->id;
        $validateData['status']  = 'pending';

        $booking = Booking::create($validateData);

        return $this->successResponse(
            $booking,
            'Booking request sent. Waiting for owner approval.',
            201
        );
    }

    public function showAllBookingForOwner()
    {
        $user = Auth::user();

        if ($user->role !== 'owner') {
            return $this->errorResponse('You are not authorized', 403);
        }

        $apartments = Apartment::where('user_id', $user->id)->pluck('id');

        $bookings = Booking::whereIn('apartment_id', $apartments)
            ->where('status', 'pending')
            ->with('user:firstName,lastName,phone', 'apartment:title,description')
            ->get();

        return $this->successResponse($bookings, 'Pending bookings');
    }

    public function approveBookingByOwner(Request $request)
    {
        $booking = Booking::findOrFail($request->id);

        if ($booking->apartment->user_id !== $request->user()->id) {
            return $this->errorResponse('You are not authorized', 403);
        }

        $old_status = $booking->status;
        $booking->status = 'approved';
        $booking->save();

        Notification::send(
            $booking->user,
            new \App\Notifications\BookingStatusChangedNotifcation($booking, $old_status)
        );

        return $this->successResponse(null, 'Booking approved successfully');
    }

    public function ownerRejectBooking(Request $request)
    {
        $booking = Booking::findOrFail($request->bookingId);

        if ($booking->apartment->owner_id !== $request->user()->id) {
            return $this->errorResponse('You are not authorized', 403);
        }

        $old_status = $booking->status;
        $booking->status = 'rejected';
        $booking->save();

        Notification::send(
            $booking->user,
            new \App\Notifications\BookingStatusChangedNotifcation($booking, $old_status)
        );

        return $this->successResponse(null, 'Booking rejected successfully');
    }

    public function modifyBooking(Request $request)
    {
        $booking = Booking::findOrFail($request->bookingId);

        if ($booking->user_id !== $request->user()->id) {
            return $this->errorResponse('You are not authorized to modify this booking', 403);
        }

        $start_date = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $end_date   = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');

        $request->merge([
            'start_date' => $start_date,
            'end_date'   => $end_date
        ]);

        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        try {
            $updatedBooking = $this->bookingService->modifyBooking(
                $booking,
                $request->start_date,
                $request->end_date
            );

            return $this->successResponse(
                $updatedBooking,
                'Modification request sent. Waiting for owner approval.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    public function cancelBooking(Request $request)
    {
        $booking = Booking::findOrFail($request->bookingId);

        if ($booking->user_id !== $request->user()->id) {
            return $this->errorResponse('You are not authorized to cancel this booking', 403);
        }

        try {
            $old_status = $booking->status;
            $canceledBooking = $this->bookingService->cancelBooking($booking);

            Notification::send(
                $booking->user,
                new \App\Notifications\BookingStatusChangedNotifcation($booking, $old_status)
            );

            return $this->successResponse(
                $canceledBooking,
                'Booking canceled successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    public function renterBookings(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'renter') {
            $bookings = Booking::where('user_id', $user->id)
                ->with(['apartment.city.province'])
                ->orderBy('start_date', 'desc')
                ->get();

            return $this->successResponse($bookings, 'Renter bookings');
        }

        if ($user->role === 'owner') {
            $apartments = Apartment::where('user_id', $user->id)
                ->with('bookings.user', 'city.province')
                ->get();

            return $this->successResponse($apartments, 'Owner apartments bookings');
        }

        return $this->errorResponse('Unauthorized', 403);
    }

    public function approveModifelyBooking(Request $request)
    {
        $booking = Booking::findOrFail($request->id);

        if ($booking->modify_status !== 'pending') {
            return $this->errorResponse('There is no modification request to approve', 400);
        }

        $booking->start_date = $booking->new_start_date;
        $booking->end_date   = $booking->new_end_date;
        $booking->new_start_date = null;
        $booking->new_end_date   = null;
        $booking->modify_status  = 'approved';
        $booking->save();

        return $this->successResponse($booking, 'Modification approved');
    }

    public function rejectModifeBooking(Request $request)
    {
        $booking = Booking::findOrFail($request->id);

        if ($booking->modify_status !== 'pending') {
            return $this->errorResponse('There is no modification request to reject', 400);
        }

        $booking->new_start_date = null;
        $booking->new_end_date   = null;
        $booking->modify_status  = 'rejected';
        $booking->save();

        return $this->successResponse($booking, 'Modification rejected');
    }
}
