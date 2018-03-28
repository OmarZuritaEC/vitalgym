<?php

use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Membership;
use Faker\Generator as Faker;

$factory->define(Membership::class, function (Faker $faker) {
    return [
        'customer_id' => function() {
            return factory(Customer::class)->create()->id;
        }
    ];
});
