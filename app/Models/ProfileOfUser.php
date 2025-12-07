<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileOfUser extends Model
{
    protected $fillable = [
        'user_id',
        'firstName',
        'lastName',
        'dateOfBirth',
        'personalPhoto',
        'IDPhoto',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
