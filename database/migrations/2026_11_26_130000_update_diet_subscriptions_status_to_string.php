<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change status column from enum to string to support 'paused', 'suspended', etc.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `diet_subscriptions` MODIFY `status` VARCHAR(30) NOT NULL DEFAULT 'pending'");
        } else {
            Schema::table('diet_subscriptions', function (Blueprint $table) {
                $table->string('status', 30)->default('pending')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `diet_subscriptions` MODIFY `status` ENUM('pending', 'active', 'expired', 'cancelled', 'suspended') NOT NULL DEFAULT 'pending'");
        }
    }
};
