<?php

namespace App\Services\Nutrition;

use App\Models\NutritionLead;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/** Assigns nutrition leads to active admin nutritionists. */
class NutritionLeadAssignmentService
{
    public function assign(NutritionLead $lead, int $userId): void
    {
        $this->ensureNutritionist($userId);

        DB::transaction(function () use ($lead, $userId): void {
            $lockedLead = NutritionLead::query()->whereKey($lead->id)->lockForUpdate()->firstOrFail();
            $lockedLead->update(['assigned_to' => $userId, 'assigned_at' => now(), 'status' => 'assigned']);
        });
    }

    /** @param array<int, int> $leadIds */
    public function bulkAssign(array $leadIds, int $userId): int
    {
        $this->ensureNutritionist($userId);

        return DB::transaction(fn (): int => NutritionLead::query()
            ->whereIn('id', $leadIds)
            ->update(['assigned_to' => $userId, 'assigned_at' => now(), 'status' => 'assigned']));
    }

    private function ensureNutritionist(int $userId): void
    {
        $isActiveAdmin = User::query()
            ->whereKey($userId)
            ->where('role', 'admin')
            ->where('is_active', true)
            ->exists();

        abort_unless($isActiveAdmin, 422, 'The selected user does not have diet-chart access.');
    }
}
