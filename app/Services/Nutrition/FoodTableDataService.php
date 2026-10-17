<?php

namespace App\Services\Nutrition;

use App\Models\Food;
use Illuminate\Http\Request;

class FoodTableDataService
{
    /** @return array<string, mixed> */
    public function response(Request $request): array
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
        $query = Food::query();
        $search = $request->input('search.value');
        if (filled($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('dish_id', 'LIKE', "%{$search}%")
                    ->orWhere('dish_name', 'LIKE', "%{$search}%")
                    ->orWhere('food_group', 'LIKE', "%{$search}%")
                    ->orWhere('slot', 'LIKE', "%{$search}%")
                    ->orWhere('diet_type', 'LIKE', "%{$search}%")
                    ->orWhere('is_veg', 'LIKE', "%{$search}%")
                    ->orWhere('region', 'LIKE', "%{$search}%");
            });
        }

        $totalFiltered = (clone $query)->count();
        $columnIndex = (int) $request->input('order.0.column', 1);
        $orderColumn = $columns[$columnIndex] ?? 'dish_name';
        $direction = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';
        $foods = $query->offset((int) $request->input('start', 0))
            ->limit((int) $request->input('length', 10))
            ->orderBy($orderColumn, $direction)
            ->get();

        $data = $foods->map(fn (Food $food) => [
            'id' => $food->id,
            'dish_id' => $food->dish_id,
            'dish_name' => $food->dish_name,
            'food_group' => $food->food_group,
            'slot' => $food->slot,
            'diet_type' => $food->diet_type,
            'is_veg' => $food->is_veg === 'Yes'
                ? '<span class="badge bg-success">Yes</span>'
                : ($food->is_veg === 'No' ? '<span class="badge bg-danger">No</span>' : $food->is_veg),
            'region' => $food->region,
            'energy_kcal' => $food->energy_kcal,
        ])->all();

        return [
            'draw' => (int) $request->input('draw', 0),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ];
    }
}
