<?php

namespace Tests\Unit\Console\Commands;

use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Membership;
use Tests\TestCase;
use Notification;
use App\Notifications\ExpiredMembership;

class ExpiredMembershipTest extends TestCase
{
    /** @test */
    function it_send_an_expired_membership_notification()
    {
        Notification::fake();

        $john = factory(Customer::class)->create();
        $jane = factory(Customer::class)->create();
        $ted  = factory(Customer::class)->create();

        factory(Membership::class)->create(['customer_id' => $john->id, 'date_end' => now()->today()]);
        factory(Membership::class)->create(['customer_id' => $john->id, 'date_end' => now()->today()]);
        factory(Membership::class)->create(['customer_id' => $jane->id, 'date_end' => now()->today()]);
        factory(Membership::class)->create(['customer_id' => $ted->id, 'date_end' => now()->subDays(5)]);

        $this->artisan('notify:customer-membership-expired');

        Notification::assertSentTo($john, ExpiredMembership::class);
        Notification::assertSentToTimes($john, ExpiredMembership::class,1);
        Notification::assertSentTo($jane, ExpiredMembership::class);
        Notification::assertSentToTimes($jane, ExpiredMembership::class,1);
        Notification::assertNotSentTo($ted, ExpiredMembership::class);
    }
}