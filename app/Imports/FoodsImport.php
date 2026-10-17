<?php

namespace App\Imports;

use App\Models\Food;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class FoodsImport implements ToModel, WithHeadingRow, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    public $imported = 0;
    public $skipped = 0;
    public $failedRows = [];
    public $existingIds = [];

    public function model(array $row)
    {
        $dishId   = trim($row['dish_id'] ?? '');
        $dishName = trim($row['dish_name'] ?? '');

        // Empty row skip
        if (empty($dishId) && empty($dishName)) {
            $this->skipped++;
            return null;
        }

        // dish_id blank ho to fail karo
        if (empty($dishId)) {
            $this->failedRows[] = [
                'dish_id'     => '(blank)',
                'dish_name'   => $dishName,
                'food_group'  => $row['food_group'] ?? '',
                'slot'        => $row['slot'] ?? '',
                'diet_type'   => $row['diet_type'] ?? '',
                'is_veg'      => $row['is_veg'] ?? '',
                'region'      => $row['region'] ?? '',
                'energy_kcal' => $row['energy_kcal'] ?? '',
                'reason'      => 'dish_id is required',
            ];
            return null;
        }

        $data = [
            'dish_id'     => $dishId,
            'dish_name'   => $dishName ?: null,
            'food_group'  => $row['food_group'] ?? null,
            'slot'        => $row['slot'] ?? null,
            'diet_type'   => $row['diet_type'] ?? null,
            'is_veg'      => $row['is_veg'] ?? null,
            'region'      => $row['region'] ?? null,
            'energy_kcal' => $row['energy_kcal'] ?? null,
        ];

        try {
            $existsInDb  = Food::where('dish_id', $dishId)->exists();
            $existsInCsv = in_array($dishId, $this->existingIds);

            if ($existsInDb || $existsInCsv) {
                $this->failedRows[] = [
                    'dish_id'     => $dishId,
                    'dish_name'   => $dishName,
                    'food_group'  => $row['food_group'] ?? '',
                    'slot'        => $row['slot'] ?? '',
                    'diet_type'   => $row['diet_type'] ?? '',
                    'is_veg'      => $row['is_veg'] ?? '',
                    'region'      => $row['region'] ?? '',
                    'energy_kcal' => $row['energy_kcal'] ?? '',
                    'reason'      => $existsInDb
                        ? 'Duplicate dish_id (already in database)'
                        : 'Duplicate dish_id (repeated in file)',
                ];
                return null;
            }

            $this->existingIds[] = $dishId;

            $this->imported++;
            return new Food($data);
        } catch (\Exception $e) {
            $this->failedRows[] = [
                'dish_id'     => $dishId,
                'dish_name'   => $dishName,
                'food_group'  => $row['food_group'] ?? '',
                'slot'        => $row['slot'] ?? '',
                'diet_type'   => $row['diet_type'] ?? '',
                'is_veg'      => $row['is_veg'] ?? '',
                'region'      => $row['region'] ?? '',
                'energy_kcal' => $row['energy_kcal'] ?? '',
                'reason'      => $e->getMessage(),
            ];
            return null;
        }
    }

    public function onFailure(\Maatwebsite\Excel\Validators\Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failedRows[] = [
                'dish_id'     => $failure->values()['dish_id'] ?? '',
                'dish_name'   => $failure->values()['dish_name'] ?? '',
                'food_group'  => $failure->values()['food_group'] ?? '',
                'slot'        => $failure->values()['slot'] ?? '',
                'diet_type'   => $failure->values()['diet_type'] ?? '',
                'is_veg'      => $failure->values()['is_veg'] ?? '',
                'region'      => $failure->values()['region'] ?? '',
                'energy_kcal' => $failure->values()['energy_kcal'] ?? '',
                'reason'      => implode(', ', $failure->errors()),
            ];
        }
    }

    public function onError(\Throwable $e)
    {
        $this->failedRows[] = [
            'dish_id'     => '',
            'dish_name'   => '',
            'food_group'  => '',
            'slot'        => '',
            'diet_type'   => '',
            'is_veg'      => '',
            'region'      => '',
            'energy_kcal' => '',
            'reason'      => $e->getMessage(),
        ];
    }
}