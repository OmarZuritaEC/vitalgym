<?php

namespace App\VitalGym\Entities;

use Illuminate\Database\Eloquent\Model;

class MembershipType extends Model
{
    public function getPriceInDollarsAttribute()
    {
        return number_format($this->price / 100, 2);
    }
}
