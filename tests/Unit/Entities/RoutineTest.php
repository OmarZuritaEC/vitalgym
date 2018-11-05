<?php

namespace Tests\Unit\Entities;

use App\VitalGym\Entities\Routine;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class RoutineTest extends TestCase
{
    /** @test */
    function a_routine_has_many_users()
    {
        $routine = factory(Routine::class)->make();

        $this->assertInstanceOf(Collection::class, $routine->customers);
    }
}
