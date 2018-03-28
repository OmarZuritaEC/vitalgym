<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\VitalGym\Entities\MembershipType;

class MembershipTypeTest extends TestCase
{
    /** @test */
    public function can_get_price_in_dollars_with_two_decimals()
    {
        $membershipType = factory(MembershipType::class)->make([
           'price' => 461,
        ]);

        $this->assertSame('4.61', $membershipType->price_in_dollars);
    }
}