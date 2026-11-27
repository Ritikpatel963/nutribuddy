<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('child_check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->foreignId('child_assessment_id')->nullable()->constrained('child_assessments')->nullOnDelete();
            $table->decimal('weight_kg', 5, 2);
            $table->decimal('height_cm', 5, 2);
            $table->unsignedTinyInteger('illness_count');
            $table->string('focus_level', 32);
            $table->string('energy_level', 32);
            $table->json('life_events')->nullable();
            $table->text('food_changes')->nullable();
            $table->timestamps();

            $table->index(['child_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('child_check_ins');
    }
};
