<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_combo')->default(false)->after('is_active')->index();
            $table->string('combo_tagline')->nullable()->after('is_combo');
            $table->string('combo_badge')->nullable()->after('combo_tagline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_combo']);
            $table->dropColumn(['is_combo', 'combo_tagline', 'combo_badge']);
        });
    }
};
