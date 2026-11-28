<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('transform_before_image')->nullable()->after('transform_main_image');
            $table->string('transform_before_label')->nullable()->after('transform_before_image');
            $table->string('transform_before_sublabel')->nullable()->after('transform_before_label');
            $table->string('transform_after_image')->nullable()->after('transform_before_sublabel');
            $table->string('transform_after_label')->nullable()->after('transform_after_image');
            $table->string('transform_after_sublabel')->nullable()->after('transform_after_label');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'transform_before_image',
                'transform_before_label',
                'transform_before_sublabel',
                'transform_after_image',
                'transform_after_label',
                'transform_after_sublabel',
            ]);
        });
    }
};
