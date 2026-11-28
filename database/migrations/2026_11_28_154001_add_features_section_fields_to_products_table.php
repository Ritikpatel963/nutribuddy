<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('features_heading')->nullable()->after('problem_cards');
            $table->string('features_title')->nullable()->after('features_heading');
            $table->text('features_subtitle')->nullable()->after('features_title');
            $table->json('features_items')->nullable()->after('features_subtitle');
            $table->string('features_comparison_title')->nullable()->after('features_items');
            $table->json('features_comparison_rows')->nullable()->after('features_comparison_title');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'features_heading',
                'features_title',
                'features_subtitle',
                'features_items',
                'features_comparison_title',
                'features_comparison_rows',
            ]);
        });
    }
};
