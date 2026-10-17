<?php

namespace App\Http\Controllers\Admin\Nutrition;

use App\Http\Controllers\Controller;
use App\Models\DietSubscription;
use Illuminate\Http\Request;

class DietSubscriptionController extends Controller
{
    use \App\Http\Controllers\Admin\ResourceWritesDisabled;
    use \App\Http\Controllers\Admin\ResourceIndexFallbacks;
    public function index()
    {
        $subscriptions = DietSubscription::with(['user', 'subscriptionPlan', 'child'])
            ->latest()
            ->paginate(15);
            
        return view('admin.nutrition.diet-subscriptions.index', compact('subscriptions'));
    }

    public function show($id)
    {
        $subscription = DietSubscription::with([
            'user', 
            'subscriptionPlan', 
            'child.assessments.answers.question'
        ])->findOrFail($id);

        return view('admin.nutrition.diet-subscriptions.show', compact('subscription'));
    }
}
