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
            $table->string('seo_image')->nullable()->after('seo_keywords');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('seo_keywords')->nullable()->after('seo_description');
            $table->string('seo_image')->nullable()->after('seo_keywords');
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->string('seo_keywords')->nullable()->after('seo_description');
            $table->string('seo_image')->nullable()->after('seo_keywords');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('seo_image');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['seo_keywords', 'seo_image']);
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->dropColumn(['seo_keywords', 'seo_image']);
        });
    }
};
