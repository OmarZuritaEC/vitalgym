<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\VitalGym\Entities\Plan;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::all();

        return view('admin.plans.index', compact('plans'));
    }
}
