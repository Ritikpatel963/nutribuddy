<?php

namespace App\Models\Nutrition;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChildCheckIn extends Model
{
    protected $fillable = [
        'child_id',
        'child_assessment_id',
        'weight_kg',
        'height_cm',
        'illness_count',
        'focus_level',
        'energy_level',
        'life_events',
        'food_changes',
    ];

    protected function casts(): array
    {
        return [
            'weight_kg' => 'float',
            'height_cm' => 'float',
            'illness_count' => 'integer',
            'life_events' => 'array',
        ];
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(ChildAssessment::class, 'child_assessment_id');
    }
}
