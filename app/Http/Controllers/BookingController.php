<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Booking;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use League\Flysystem\UrlGeneration\TemporaryUrlGenerator;

class BookingController extends Controller
{
    protected  $bookingService;

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
$validateData['user_id'] =$request->user()->id;
         if($request->user()->role != 'renter')
         {
             return response()->json
                ([
                'error' => 'Only renters can make bookings'
                 ], 403);
         }

        $hasConflict = $this->bookingService->hasConflict(
            $request->apartment_id,
            $request->start_date,
            $request->end_date
        );

          if ($hasConflict) {
            return response()->json([
                'error' => 'This apartment is already booked in this period'
            ], 409);
        }
        $validateData['status'] = 'pending';
        $booking = Booking::create(
            $validateData
        );
        return response()->json([
            'message' => 'Booking request sent. Waiting for owner approval.',
            'data'    => $booking
        ], 201);
}
   public function modifyBooking(Request $request )
   {
    $booking = Booking::findOrFail($request->bookingId);
    if($booking->user_id != $request->user()->id)
    {
        return response()->json([
            'error' => 'You are not authorized to modify this booking.'
        ],403);
    }
    $start_date = Carbon::createFromFormat('d/m/Y',$request->start_date)->format('Y-m-d');
    $end_date   = Carbon::createFromFormat('d/m/Y',$request->end_date)->format('Y-m-d');

    $request->merge([
        'start_date' => $start_date,
        'end_date'   => $end_date
    ]);
    $validateData = $request->validate([
        'start_date' => 'required|date',
        'end_date'   => 'required|date|after:new_start_date',
    ]);
    try{
        $updatedBooking = $this->bookingService->modifyBooking(
            $booking,
            $request->start_date,
            $request->end_date
        );
        return response()->json([
            'message' => 'Booking modified successfully.',
            'data'    => $updatedBooking
        ],200);
    }catch(\Exception $e)
    {
     return response()->json(['error' => $e->getMessage()], 400);
    }}
    public function cancelBooking(Request $request )
    {
          $booking = Booking::findOrFail($request->bookingId);
           if($booking->user_id != $request->user()->id)
           {
   return response()->json([
            'error' => 'You are not authorized to modify this booking.'
        ],403);
           }
           try
           {
            $canceledBooking = $this->bookingService->cancelBooking($booking);
            return response()->json([
                'message' => 'Booking canceled successfully.',
                'data'    => $canceledBooking
            ],200);
           }
           catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 400);
    }
    }
     public function renterBookings(Request $request)
     {
         $user = $request->user();
         if($user->role == 'renter')
         {
            $bookings = Booking::where('user_id',$user->id)
            ->with(['apartment.city.province'])
            ->orderBy('start_date','desc')
            ->get()
            ->makeHidden(['id','user_id','apartment_id','created_at','updated_at']);
           $bookings->each(function($booking){
           $booking->apartment->makeHidden(['id','user_id','city_id','address','description','created_at','updated_at']);
           $booking->apartment->city->makeHidden(['id','province_id','created_at','updated_at']);
           $booking->apartment->city->province->makeHidden(['id','created_at','updated_at']);
             });
             $result = $bookings->map(function($booking){
                return [
                    'apartment'=>$booking->apartment,
                    'start_date'=>Carbon::parse($booking->start_date)->format('d/m/Y'),
                    'end_date'=>Carbon::parse($booking->end_date)->format('d/m/Y'),
                    'status'=>$booking->status
                ];
             });
             return response()->json
             ([
                  'type'=>'renter',
                  'booking'=>$result
             ]);}
             else
             {
               $apartments = Apartment::where('user_id',$user->id)
               ->with('bookings.user','city.province')
               ->get()
               ->makeHidden(['id','user_id','city_id','address','description','created_at','updated_at']);
            $apartments->each(function($apt){
$apt-> city->makeHidden(['id','province_id','created_at','updated_at']);
$apt-> city->province->makeHidden(['id','created_at','updated_at']);
$apt->bookings->each(function($booking){
    $booking->makeHidden(['id','user_id','apartment_id','created_at','updated_at']);
    $booking->user->makeHidden(['id','created_at','updated_at']);
  });  });
    return response()->json([
        'type'=>'owner',
        'apartments'=>$apartments

    ]);
     }
     return response()->json
     ([
          'error'=>'Unauthorized'
     ],403);
    }

}
