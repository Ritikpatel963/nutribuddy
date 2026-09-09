<?php
/**
 * QUICK TEST SCRIPT - Verify ChildAssessment data is being saved
 * 
 * Usage: Place in project root and run via artisan or PHP
 * php TEST_DATA_SAVING.php
 * 
 * Or use tinker:
 * php artisan tinker < TEST_DATA_SAVING.php
 */

// Test 1: Create a child and assessment directly using the model
echo "\n========== TEST 1: Direct Model Creation ==========\n";

try {
    // First, create a test user (or use existing)
    $user = \App\Models\User::first(); // Get first user
    
    if (!$user) {
        echo "❌ No users found in database. Please create a user first.\n";
        exit(1);
    }
    
    echo "✓ Using User ID: {$user->id}\n";
    
    // Create a test child
    $child = \App\Models\Nutrition\Child::create([
        'user_id' => $user->id,
        'name' => 'Test Child - ' . date('Y-m-d H:i:s'),
        'dob' => now()->subYears(5),
        'sex' => 'boy',
    ]);
    
    echo "✓ Child created: ID={$child->id}, Name={$child->name}\n";
    
    // Create an assessment with actual data
    $assessment = \App\Models\Nutrition\ChildAssessment::create([
        'child_id' => $child->id,
        'model_level' => 'champion',
        'current_weight_kg' => 18.5,
        'current_height_cm' => 106.0,
        'diet_type' => 'Pure vegetarian',
        'allergies' => null,
        'region' => 'Gujarat / West India',
    ]);
    
    echo "✓ Assessment created: ID={$assessment->id}\n";
    
    // Verify data was actually saved
    $assessment->refresh();
    
    echo "\n✓✓✓ DATA SAVED TO DATABASE ✓✓✓\n";
    echo "Weight: {$assessment->current_weight_kg} kg\n";
    echo "Height: {$assessment->current_height_cm} cm\n";
    echo "Diet Type: {$assessment->diet_type}\n";
    echo "Region: {$assessment->region}\n";
    
    if ($assessment->current_weight_kg === 18.5 && $assessment->current_height_cm === 106.0) {
        echo "\n✅ SUCCESS: Data was correctly saved to database!\n";
    } else {
        echo "\n❌ FAILED: Data was not saved correctly!\n";
        exit(1);
    }
    
} catch (\Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    echo "Stack trace: {$e->getTraceAsString()}\n";
    exit(1);
}

echo "\n========== TEST 2: Verify $fillable is working ==========\n";

try {
    $model = new \App\Models\Nutrition\ChildAssessment();
    $fillable = $model->getFillable();
    
    echo "Fillable fields in ChildAssessment:\n";
    foreach ($fillable as $field) {
        echo "  - $field\n";
    }
    
    $requiredFields = ['child_id', 'current_weight_kg', 'current_height_cm', 'diet_type', 'region'];
    $missing = array_diff($requiredFields, $fillable);
    
    if (empty($missing)) {
        echo "\n✅ All required fields are in \$fillable\n";
    } else {
        echo "\n❌ Missing fields in \$fillable: " . implode(', ', $missing) . "\n";
        exit(1);
    }
    
} catch (\Exception $e) {
    echo "❌ Error checking fillable: {$e->getMessage()}\n";
    exit(1);
}

echo "\n========== ALL TESTS PASSED ✅ ==========\n";
