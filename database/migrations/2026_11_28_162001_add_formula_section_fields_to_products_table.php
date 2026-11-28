<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('formula_eyebrow')->nullable()->after('features_comparison_rows');
            $table->string('formula_heading')->nullable()->after('formula_eyebrow');
            $table->string('formula_subheading')->nullable()->after('formula_heading');
            $table->string('formula_tag')->nullable()->after('formula_subheading');
            $table->string('formula_bg_word')->nullable()->after('formula_tag');
            $table->string('formula_image')->nullable()->after('formula_bg_word');
            $table->string('formula_stat_left_num')->nullable()->after('formula_image');
            $table->string('formula_stat_left_title')->nullable()->after('formula_stat_left_num');
            $table->string('formula_stat_left_sub')->nullable()->after('formula_stat_left_title');
            $table->string('formula_stat_right_num')->nullable()->after('formula_stat_left_sub');
            $table->string('formula_stat_right_title')->nullable()->after('formula_stat_right_num');
            $table->string('formula_stat_right_sub')->nullable()->after('formula_stat_right_title');
            $table->json('formula_badges')->nullable()->after('formula_stat_right_sub');
            $table->string('formula_caption')->nullable()->after('formula_badges');
            $table->string('formula_button_primary_text')->nullable()->after('formula_caption');
            $table->string('formula_button_primary_url')->nullable()->after('formula_button_primary_text');
            $table->string('formula_button_secondary_text')->nullable()->after('formula_button_primary_url');
            $table->string('formula_button_secondary_url')->nullable()->after('formula_button_secondary_text');
            $table->string('formula_footnote')->nullable()->after('formula_button_secondary_url');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'formula_eyebrow',
                'formula_heading',
                'formula_subheading',
                'formula_tag',
                'formula_bg_word',
                'formula_image',
                'formula_stat_left_num',
                'formula_stat_left_title',
                'formula_stat_left_sub',
                'formula_stat_right_num',
                'formula_stat_right_title',
                'formula_stat_right_sub',
                'formula_badges',
                'formula_caption',
                'formula_button_primary_text',
                'formula_button_primary_url',
                'formula_button_secondary_text',
                'formula_button_secondary_url',
                'formula_footnote',
            ]);
        });
    }
};
