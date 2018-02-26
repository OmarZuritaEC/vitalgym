<?php

namespace Tests\Feature;

use App\VitalGym\Entities\MembershipType;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListingMemberShipTypesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function listing_membership_types(){
        $user = $this->createNewUser();

        factory(MembershipType::class)->create(['name' => 'mensual', 'price' => 2550]);
        factory(MembershipType::class)->create(['name' => 'semestral', 'price' => 20000]);
        factory(MembershipType::class)->create(['name' => 'anual', 'price' => 28050]);

        $response = $this->actingAs($user)->get(route('membership-types'));

        $response->assertStatus(200);
        $response->assertSee('mensual');
        $response->assertSee('25.50');
        $response->assertSee('semestral');
        $response->assertSee('200.00');
        $response->assertSee('anual');
        $response->assertSee('280.50');
    }
}