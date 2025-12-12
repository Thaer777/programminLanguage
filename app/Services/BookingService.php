<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;

class BookingService
{
    public function hasConflict($apartmentId,$startDate,$endDate , $excludeBookingId = null)
    {

  $startDate = \Carbon\Carbon::parse($startDate)->toDateString();
    $endDate   = \Carbon\Carbon::parse($endDate)->toDateString();

    $query = Booking::where('apartment_id', $apartmentId);


    if ($excludeBookingId) {
        $query->where('id', '!=', $excludeBookingId);
    }

    $conflict = $query->where(function($q) use ($startDate, $endDate) {
        $q->whereBetween('start_date', [$startDate, $endDate])
          ->orWhereBetween('end_date', [$startDate, $endDate])
          ->orWhere(function($q2) use ($startDate, $endDate) {
              $q2->where('start_date', '<=', $startDate)
                 ->where('end_date', '>=', $endDate);
          });
    })->exists();

    return $conflict;
    }
    public function modifyBooking(Booking $booking,$newStartDate,$newEndDate)
    {
       if(Carbon::now('UTC')->toDateString() >= $booking->end_date)
       {
        throw new \Exception("Cannot modify past bookings.");
       }
       if($this->hasConflict($booking->apartment_id,$newStartDate,$newEndDate,$booking->id))
       {
        throw new \Exception("The new dates conflict with an existing booking.");
       }
       if($booking->status !='approved')
       {
        throw new \Exception("Only approved bookings can be modified.");
       }
       $booking->new_start_date = $newStartDate;
       $booking->new_end_date = $newEndDate;
       $booking->modify_status  = 'pending';
       $booking->save();
       return $booking;
    }
    public function cancelBooking(Booking $booking)
    {
        if(Carbon::now('UTC')->toDateString() >= $booking->end_date)
        {
            throw new \Exception('Cannot cancel past bookings');
        }
        if($booking->status == 'cancelled')
        {
            throw new \Exception('Booking is already cancelled');
        }
        $booking->status = 'cancelled';
        $booking->save();
        return $booking;


    }
}
