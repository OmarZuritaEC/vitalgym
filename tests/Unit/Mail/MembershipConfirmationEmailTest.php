<?php

namespace tests\Unit\Mail;

use Tests\TestCase;
use App\VitalGym\Entities\User;
use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Membership;
use App\Mail\MembershipConfirmationEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MembershipConfirmationEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function email_contain_the_customer_name()
    {
        $user = factory(User::class)->create([
           'name' => 'John',
        ]);

        $customer = factory(Customer::class)->create([
            'user_id' => $user->id,
        ]);

        $membership = factory(Membership::class)->create([
            'customer_id' => $customer->id,
        ]);

        $email = new MembershipConfirmationEmail($membership);
        $rendered = $email->render();

        $this->assertContains('John', $rendered);
    }
}