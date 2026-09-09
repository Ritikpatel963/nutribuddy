<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Simulate HTTP request
$request = \Illuminate\Http\Request::create('/');
$response = $kernel->handle($request);

// Now debug
use App\Models\Nutrition\Child;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "=== DEBUGGING AUTHORIZATION ISSUE ===\n\n";

// Get first child
$child = Child::first();
if (!$child) {
    echo "❌ No children found in database\n";
    echo "   Action: Create a child profile first\n";
    exit(1);
}

echo "✅ Child found:\n";
echo "   ID: " . $child->id . "\n";
echo "   Name: " . $child->name . "\n";
echo "   User ID (child->user_id): " . $child->user_id . "\n";

// Check if user exists
$user = User::find($child->user_id);
if (!$user) {
    echo "\n❌ User not found for user_id: " . $child->user_id . "\n";
    echo "   Action: User was deleted or ID is incorrect\n";
    exit(1);
}

echo "\n✅ User found:\n";
echo "   ID: " . $user->id . "\n";
echo "   Email: " . $user->email . "\n";

// Check assessment
$assessment = $child->assessments()->latest()->first();
if (!$assessment) {
    echo "\n⚠️  No assessment found for this child\n";
    echo "   Action: Complete the questionnaire first\n";
} else {
    echo "\n✅ Assessment found:\n";
    echo "   ID: " . $assessment->id . "\n";
    echo "   Diet Type: " . $assessment->diet_type . "\n";
    
    // Check diet plan
    $plan = $assessment->dietPlans()->where('status', 'active')->latest()->first();
    if (!$plan) {
        echo "\n⚠️  No active diet plan found\n";
        echo "   Action: Generate a diet plan\n";
    } else {
        echo "\n✅ Diet plan found:\n";
        echo "   ID: " . $plan->id . "\n";
        echo "   Status: " . $plan->status . "\n";
        
        // Check meals
        $meals = $plan->meals()->count();
        echo "   Meals: " . $meals . " (should be 28)\n";
    }
}

echo "\n=== TO REPRODUCE THE ERROR ===\n";
echo "1. Login as user with email: " . $user->email . "\n";
echo "2. Visit: /child/" . $child->id . "/diet-chart\n";
echo "3. If you see 'Unauthorized access', then:\n";
echo "   - The authenticated user ID doesn't match child->user_id\n";
echo "   - Or you're not actually logged in\n\n";

echo "=== AUTHORIZATION CHECK ===\n";
echo "For authorization to pass:\n";
echo "   Auth::id() === \$child->user_id\n";
echo "   " . ($user->id) . " === " . ($child->user_id) . "\n";
if ($user->id === $child->user_id) {
    echo "   ✅ MATCH - Authorization should pass\n";
} else {
    echo "   ❌ NO MATCH - Authorization will FAIL\n";
}

echo "\n";
