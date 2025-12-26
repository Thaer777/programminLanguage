<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    protected $guarded = [];
   public function amenities()
   {
       return $this->belongsToMany(Amenitie::class, 'apartment_amenity', 'apartment_id', 'amenity_id');
   }
//    public function phones()
//    {
//        return $this->hasMany(Phone::class);
//    }
   public function user()
   {
       return $this->belongsTo(User::class);
   }
   public function city()
   {
       return $this->belongsTo(City::class);
   }
   public function bookings()
   {
    return $this->hasMany(Booking::class);
   }
    public function routings()
    {
     return $this->hasManyThrough(Rating::class, Booking::class);
    }
    public function images()
{
    return $this->hasMany(ApartmentImage::class);
}
public function favoritedBy()
{
    return $this->belongsToMany(User::class, 'favorites')
                ->withTimestamps();
}

}
