<?php

namespace App\Models;

use App\Services\BookingService;
use Illuminate\Database\Eloquent\Model;

use Symfony\Component\HttpFoundation\Request;

class Booking extends Model
{
    protected $guarded = [];
    public function user()
    {
         return $this->belongsTo(User::class);
    }
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

}
