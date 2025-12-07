<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amenitie extends Model
{
    public function apartments()
    {
        return $this->belongsToMany(Apartment::class, 'apartment_amenity');
    }
}
