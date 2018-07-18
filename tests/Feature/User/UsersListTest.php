<?php

namespace Tests\Features\User;

use Tests\TestCase;
use App\VitalGym\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function the_users_are_paginated_and_sorted_by_id_in_descending_order()
    {
        $user = $this->createNewUser([
            'api_token' => str_random(60),
        ]);

        factory(User::class, 20)->create();

        $response = $this->json('GET', '/api/v1/users', [
            'api_token' => $user->api_token,
        ]);

        $users = User::orderBy('id', 'DESC')->get()->take(15)->toArray();

        $response->assertStatus(200)
            ->assertJson([
                'current_page'  => 1,
                'total'         => 21,
                'per_page'      => 15,
                'last_page'     => 2,
                'next_page_url' => config('app.url').'/api/v1/users?page=2',
                'prev_page_url' => null,
                'from'          => 1,
                'to'            => 15,
                'data'          => $users,
            ]);
    }

    /** @test */
    function the_users_without_api_token_can_not_get_user_list_data()
    {
        $response = $this->json('GET', '/api/v1/users');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    /** @test */
    function the_users_with_invalid_api_token_can_not_get_users_list_data()
    {
        $response = $this->json('GET', '/api/v1/users', [
            'api_token' => 'abc',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    /** @test */
    function the_users_can_be_filtered()
    {
        $user = $this->createNewUser([
            'name' => 'Edwin',
            'api_token' => str_random(60),
        ]);

        $expectedUsers = factory(User::class, 1)->create(['name' => 'Nadia']);

        factory(User::class, 4)->create();

        $unexpectedUsers = User::whereNotIn('name', ['Nadia'])->orderBy('id', 'DESC')->get()->take(15)->toArray();

        $response = $this->json('GET', '/api/v1/users?filter=Nadia', [
            'api_token' => $user->api_token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'current_page'  => 1,
                'total'         => 1,
                'per_page'      => 15,
                'last_page'     => 1,
                'next_page_url' => null,
                'prev_page_url' => null,
                'from'          => 1,
                'to'            => 1,
                'data'          => $expectedUsers->toArray(),
            ])
            ->assertJsonMissing([
                'data'          => $unexpectedUsers,
            ]);
    }
}
