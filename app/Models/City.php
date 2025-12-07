<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }
    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'city_id');
    }
}
