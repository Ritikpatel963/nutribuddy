<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DietSubscription;
use Illuminate\Http\Request;

class DietSubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = DietSubscription::with(['user', 'subscriptionPlan'])
            ->latest()
            ->paginate(15);
            
        return view('admin.diet-subscriptions.index', compact('subscriptions'));
    }
}
