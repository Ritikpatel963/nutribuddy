<?php

namespace App\Http\Controllers\Admin\Nutrition;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    public function index()
    {
        return view('admin.nutrition.foods.index');
    }

    public function data(Request $request)
    {
        $columns = [
            0 => 'dish_id',
            1 => 'dish_name',
            2 => 'food_group',
            3 => 'slot',
            4 => 'diet_type',
            5 => 'is_veg',
            6 => 'region',
            7 => 'energy_kcal',
        ];

        $totalData = Food::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length') ?? 10;
        $start = $request->input('start') ?? 0;
        $orderColumnIndex = $request->input('order.0.column');
        $order = $orderColumnIndex !== null ? $columns[$orderColumnIndex] : 'dish_name';
        $dir = $request->input('order.0.dir') ?? 'asc';

        $query = Food::query();

        $search = $request->input('search.value');
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('dish_id', 'LIKE', "%{$search}%")
                  ->orWhere('dish_name', 'LIKE', "%{$search}%")
                  ->orWhere('food_group', 'LIKE', "%{$search}%")
                  ->orWhere('slot', 'LIKE', "%{$search}%")
                  ->orWhere('diet_type', 'LIKE', "%{$search}%")
                  ->orWhere('is_veg', 'LIKE', "%{$search}%")
                  ->orWhere('region', 'LIKE', "%{$search}%");
            });

            $totalFiltered = $query->count();
        }

        $foods = $query->offset($start)
                       ->limit($limit)
                       ->orderBy($order, $dir)
                       ->get();

        $data = [];
        foreach ($foods as $food) {
            $nestedData = [];
            $nestedData['dish_id'] = $food->dish_id;
            $nestedData['dish_name'] = $food->dish_name;
            $nestedData['food_group'] = $food->food_group;
            $nestedData['slot'] = $food->slot;
            $nestedData['diet_type'] = $food->diet_type;
            
            // Format is_veg badge
            $isVegBadge = $food->is_veg == 'Yes' 
                ? '<span class="badge bg-success">Yes</span>' 
                : ($food->is_veg == 'No' ? '<span class="badge bg-danger">No</span>' : $food->is_veg);
            $nestedData['is_veg'] = $isVegBadge;
            
            $nestedData['region'] = $food->region;
            $nestedData['energy_kcal'] = $food->energy_kcal;

            $data[] = $nestedData;
        }

        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        ];

        return response()->json($json_data);
    }
}
