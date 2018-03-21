<?php

namespace App\VitalGym\Entities;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
