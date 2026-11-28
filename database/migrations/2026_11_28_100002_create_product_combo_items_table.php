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
        Schema::create('product_combo_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combo_product_id')
                ->constrained('products')
                ->onDelete('cascade');
            $table->foreignId('child_variant_id')
                ->constrained('product_variants')
                ->onDelete('cascade');
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->unique(['combo_product_id', 'child_variant_id'], 'combo_product_child_variant_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_combo_items');
    }
};
