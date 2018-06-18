<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\VitalGym\Entities\Payment;
use App\VitalGym\Entities\Customer;
use Illuminate\Support\Facades\Mail;
use App\VitalGym\Entities\MembershipType;
use App\Mail\MembershipOrderConfirmationEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class AddMembershipTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    private $response;
    private $adminUser;

    public function setUp()
    {
        parent::setUp();
        $this->adminUser = $this->createNewUser();
    }

    private function orderMembership($params = [])
    {
        $savedRequest = $this->app['request'];
        $this->response = $this->actingAs($this->adminUser)->json('POST', route('admin.membership.store'), $params);
        $this->app['request'] = $savedRequest;
    }

    private function assertValidationError($field)
    {
        $this->response->assertStatus(422);
        $this->response->assertJsonValidationErrors($field);
    }

    /** @test */
    function create_membership_for_a_new_customer()
    {
        $this->withoutExceptionHandling();
        Mail::fake();

        $customerUser = $this->createNewUser([
            'name' =>'John',
            'last_name' => 'Doe',
            'role' => 'customer',
            'email' => 'john@example.com',
        ]);

        $dateStart = Carbon::now()->toDateString();
        $dateEnd = Carbon::now()->addMonth(1)->toDateString();

        $membershipType = factory(MembershipType::class)->create(['name' =>'Mensual', 'price' => 3000]);
        $customer = factory(Customer::class)->create(['user_id' => $customerUser->id]);

        $this->orderMembership([
            'date_start' => $dateStart,
            'date_end' => $dateEnd,
            'total_days' => 30,
            'membership_type_id' => $membershipType->id,
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ]);

        $this->response->assertStatus(201);

        $this->response->assertJson([
            'date_start'  => $dateStart,
            'date_end'    => $dateEnd,
            'total_days'  => 30,
            'name'        => 'Mensual',
            'unit_price'  => 3000,
            'created_by' => $this->adminUser->full_name,
            'total_price' => 6000,
            'membership_quantity' => 2,
            'customer' => [
                'name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
            ],
        ]);

        $membership = $customer->memberships->fresh()->last();
        $this->assertNotNull($membership);
        $payment = Payment::where('membership_id', $membership->id)->first();
        $this->assertEquals(30, $membership->total_days);
        $this->assertEquals($dateStart, $membership->date_start->toDateString());
        $this->assertEquals($dateEnd, $membership->date_end->toDateString());
        $this->assertEquals($membership->id, $payment->membership_id);
        $this->assertEquals(2, $payment->membership_quantity);
        $this->assertEquals($customer->id, $payment->customer_id);
        $this->assertEquals(6000, $payment->total_price);
        $this->assertEquals($this->adminUser->id, $payment->user_id);

        Mail::assertSent(MembershipOrderConfirmationEmail::class, function ($mail) use ($membership) {
            return $mail->hasTo('john@example.com')
                && $mail->membership->id === $membership->id;
        });
    }

    /** @test */
    function date_start_is_required_to_create_a_membership()
    {
        $this->withExceptionHandling();
        $membershipType = factory(MembershipType::class)->create();
        $customer = factory(Customer::class)->create();

        $this->orderMembership([
            'date_end' => Carbon::now()->toDateString(),
            'total_days' => 30,
            'membership_type_id' => $membershipType->id,
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ]);

        $this->assertValidationError('date_start');
    }

    /** @test */
    function date_start_must_be_a_valid_date_to_create_a_membership()
    {
        $this->withExceptionHandling();
        $membershipType = factory(MembershipType::class)->create();
        $customer = factory(Customer::class)->create();

        $this->orderMembership([
            'date_start' => 'invalid-start-date',
            'date_end' => Carbon::now()->toDateString(),
            'total_days' => 30,
            'membership_type_id' => $membershipType->id,
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ]);

        $this->response->assertJsonValidationErrors('date_start');
    }

    /** @test */
    function date_start_must_be_greater_or_equal_than_the_current_date_to_create_a_membership()
    {
        $this->withExceptionHandling();
        $membershipType = factory(MembershipType::class)->create();
        $customer = factory(Customer::class)->create();

        $this->orderMembership([
            'date_start' => '1998-06-05',
            'date_end' => Carbon::now()->toDateString(),
            'total_days' => 30,
            'membership_type_id' => $membershipType->id,
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ]);

        $this->assertValidationError('date_start');
    }

    /** @test */
    function date_start_must_have_the_following_format_yyyy_mm_dd_to_create_a_membership()
    {
        $this->withExceptionHandling();
        $membershipType = factory(MembershipType::class)->create();
        $customer = factory(Customer::class)->create();

        $this->orderMembership([
            'date_start' => Carbon::now()->format('d-m-Y'),
            'date_end' => Carbon::now()->toDateString(),
            'total_days' => 30,
            'membership_type_id' => $membershipType->id,
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ]);

        $this->assertValidationError('date_start');
    }

    /** @test */
    function date_end_is_required_to_create_a_membership()
    {
        $this->withExceptionHandling();
        $membershipType = factory(MembershipType::class)->create();
        $customer = factory(Customer::class)->create();

        $this->orderMembership([
            'date_start' => Carbon::now()->toDateString(),
            'total_days' => 30,
            'membership_type_id' => $membershipType->id,
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ]);

        $this->assertValidationError('date_end');
    }

    /** @test */
    function date_end_must_be_a_valid_date_to_create_a_membership()
    {
        $this->withExceptionHandling();
        $membershipType = factory(MembershipType::class)->create();
        $customer = factory(Customer::class)->create();

        $this->orderMembership([
            'date_start' => Carbon::now()->toDateString(),
            'date_end' => 'invalid-end-date',
            'total_days' => 30,
            'membership_type_id' => $membershipType->id,
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ]);

        $this->response->assertJsonValidationErrors('date_end');
    }

    /** @test */
    function date_end_must_have_the_following_format_yyyy_mm_dd_to_create_a_membership()
    {
        $this->withExceptionHandling();
        $membershipType = factory(MembershipType::class)->create();
        $customer = factory(Customer::class)->create();

        $this->orderMembership([
            'date_start' => Carbon::now()->toDateString(),
            'date_end' => Carbon::now()->format('d-m-Y'),
            'total_days' => 30,
            'membership_type_id' => $membershipType->id,
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ]);

        $this->assertValidationError('date_end');
    }

    /** @test */
    function the_end_date_must_be_greater_or_equal_than_start_date_to_create_a_membership()
    {
        $this->withExceptionHandling();
        $membershipType = factory(MembershipType::class)->create();
        $customer = factory(Customer::class)->create();
        $date = Carbon::now();

        $this->orderMembership([
            'date_start' => $date->toDateString(),
            'date_end' => $date->subDays(10)->toDateString(),
            'total_days' => 30,
            'membership_type_id' => $membershipType->id,
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ]);

        $this->assertValidationError('date_end');
    }

    /** @test */
    function the_total_days_must_be_integer()
    {
        $this->withExceptionHandling();
        $membershipType = factory(MembershipType::class)->create();
        $customer = factory(Customer::class)->create();
        $date = Carbon::now();

        $this->orderMembership([
            'date_start' => $date->toDateString(),
            'date_end' => $date->addDays(30)->toDateString(),
            'total_days' => 'invalid-number-days',
            'membership_type_id' => $membershipType->id,
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ]);

        $this->assertValidationError('total_days');
    }

    /** @test */
    function the_total_days_must_be_required_to_create_a_membership()
    {
        $this->withExceptionHandling();
        $membershipType = factory(MembershipType::class)->create();
        $customer = factory(Customer::class)->create();
        $date = Carbon::now();

        $this->orderMembership([
            'date_start' => $date->toDateString(),
            'date_end' => $date->addDays(30)->toDateString(),
            'membership_type_id' => $membershipType->id,
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ]);

        $this->assertValidationError('total_days');
    }

    /** @test */
    function total_days_must_be_at_least_1_to_create_a_membership()
    {
        $this->withExceptionHandling();
        $membershipType = factory(MembershipType::class)->create();
        $customer = factory(Customer::class)->create();
        $date = Carbon::now();

        $this->orderMembership([
            'date_start' => $date->toDateString(),
            'date_end' => $date->addDays(30)->toDateString(),
            'total_days' => 0,
            'membership_type_id' => $membershipType->id,
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ]);

        $this->assertValidationError('total_days');
    }

    /** @test */
    function membership_type_is_required_to_create_a_membership()
    {
        $this->withExceptionHandling();
        $customer = factory(Customer::class)->create();
        $date = Carbon::now();

        $this->orderMembership([
            'date_start' => $date->toDateString(),
            'date_end' => $date->addDays(30)->toDateString(),
            'total_days' => 30,
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ]);

        $this->assertValidationError('membership_type_id');
    }

    /** @test */
    function membership_type_must_exist_to_create_a_membership()
    {
        $this->withExceptionHandling();
        $customer = factory(Customer::class)->create();
        $date = Carbon::now();

        $this->orderMembership([
            'date_start' => $date->toDateString(),
            'date_end' => $date->addDays(30)->toDateString(),
            'total_days' => 30,
            'membership_type_id' => 101,
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ]);

        $this->assertValidationError('membership_type_id');
    }

    /** @test */
    function the_customer_is_required_to_create_a_membership()
    {
        $this->withExceptionHandling();
        $membershipType = factory(MembershipType::class)->create();
        $date = Carbon::now();

        $this->orderMembership([
            'date_start' => $date->toDateString(),
            'date_end' => $date->addDays(30)->toDateString(),
            'total_days' => 30,
            'membership_type_id' => $membershipType->id,
            'membership_quantity' => 2,
        ]);

        $this->assertValidationError('customer_id');
    }

    /** @test */
    function customer_must_exist_to_create_a_membership()
    {
        $this->withExceptionHandling();
        $membershipType = factory(MembershipType::class)->create();
        $date = Carbon::now();

        $this->orderMembership([
            'date_start' => $date->toDateString(),
            'date_end' => $date->addDays(30)->toDateString(),
            'total_days' => 30,
            'membership_type_id' => $membershipType->id,
            'customer_id' => 1,
            'membership_quantity' => 2,
        ]);

        $this->assertValidationError('customer_id');
    }

    /** @test */
    function membership_quantity_is_required_to_create_membership()
    {
        $this->withExceptionHandling();
        $membershipType = factory(MembershipType::class)->create();
        $customer = factory(Customer::class)->create();
        $date = Carbon::now();

        $this->orderMembership([
            'date_start' => $date->toDateString(),
            'date_end' => $date->addDays(30)->toDateString(),
            'total_days' => 30,
            'membership_type_id' => $membershipType->id,
            'customer_id' => $customer->id,
        ]);

        $this->assertValidationError('membership_quantity');
    }

    /** @test */
    function membership_quantity_must_be_a_integer_to_create_membership()
    {
        $this->withExceptionHandling();
        $membershipType = factory(MembershipType::class)->create();
        $customer = factory(Customer::class)->create();
        $date = Carbon::now();

        $this->orderMembership([
            'date_start' => $date->toDateString(),
            'date_end' => $date->addDays(30)->toDateString(),
            'total_days' => 30,
            'membership_type_id' => $membershipType->id,
            'customer_id' => $customer->id,
            'membership_quantity' => 'invalid-membership-quantity',
        ]);

        $this->assertValidationError('membership_quantity');
    }

    /** @test */
    function membership_quantity_must_be_at_least_1_to_create_membership()
    {
        $this->withExceptionHandling();
        $membershipType = factory(MembershipType::class)->create();
        $customer = factory(Customer::class)->create();
        $date = Carbon::now();

        $this->orderMembership([
            'date_start' => $date->toDateString(),
            'date_end' => $date->addDays(30)->toDateString(),
            'total_days' => 30,
            'membership_type_id' => $membershipType->id,
            'customer_id' => $customer->id,
            'membership_quantity' => 0,
        ]);

        $this->assertValidationError('membership_quantity');
    }
}
