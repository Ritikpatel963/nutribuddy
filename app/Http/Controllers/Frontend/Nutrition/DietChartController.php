<?php

namespace App\Http\Controllers\Frontend\Nutrition;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DietChartController extends Controller
{
    public function show($childId)
    {
        // For demonstration, we'll generate a beautiful 7-day mocked diet plan
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $meals = ['Breakfast', 'Morning Snack', 'Lunch', 'Evening Snack', 'Dinner'];
        
        $dietPlan = [];
        
        // Some mockup data for variety
        $mockMeals = [
            'Breakfast' => [
                ['name' => 'Oatmeal with Berries', 'calories' => 250, 'protein' => 8],
                ['name' => 'Scrambled Eggs on Toast', 'calories' => 300, 'protein' => 14],
                ['name' => 'Pancakes with Maple Syrup', 'calories' => 350, 'protein' => 6],
                ['name' => 'Avocado Toast', 'calories' => 280, 'protein' => 7]
            ],
            'Morning Snack' => [
                ['name' => 'Apple Slices with Peanut Butter', 'calories' => 150, 'protein' => 4],
                ['name' => 'Greek Yogurt', 'calories' => 120, 'protein' => 10],
                ['name' => 'Handful of Almonds', 'calories' => 160, 'protein' => 6]
            ],
            'Lunch' => [
                ['name' => 'Grilled Chicken Salad', 'calories' => 400, 'protein' => 25],
                ['name' => 'Quinoa Bowl with Veggies', 'calories' => 380, 'protein' => 12],
                ['name' => 'Turkey Wrap', 'calories' => 350, 'protein' => 20],
                ['name' => 'Lentil Soup with Bread', 'calories' => 320, 'protein' => 15]
            ],
            'Evening Snack' => [
                ['name' => 'Carrot Sticks with Hummus', 'calories' => 130, 'protein' => 3],
                ['name' => 'Cheese String', 'calories' => 80, 'protein' => 6],
                ['name' => 'Fruit Smoothie', 'calories' => 180, 'protein' => 5]
            ],
            'Dinner' => [
                ['name' => 'Baked Salmon with Asparagus', 'calories' => 450, 'protein' => 30],
                ['name' => 'Spaghetti Bolognese', 'calories' => 500, 'protein' => 22],
                ['name' => 'Stir-fried Tofu with Rice', 'calories' => 420, 'protein' => 18],
                ['name' => 'Chicken Tacos', 'calories' => 460, 'protein' => 24]
            ]
        ];

        foreach ($days as $index => $day) {
            $dailyPlan = [];
            foreach ($meals as $mealTime) {
                // Pick a random meal for variety, using day index to keep it deterministic
                $options = $mockMeals[$mealTime];
                $choice = $options[$index % count($options)];
                $dailyPlan[$mealTime] = $choice;
            }
            $dietPlan[$day] = $dailyPlan;
        }

        return view('frontend.nutrition.diet_chart', compact('dietPlan', 'days', 'childId'));
    }
}
