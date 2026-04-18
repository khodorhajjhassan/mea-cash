<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table): void {
            $table->json('subcategory_ids')->nullable()->after('subcategory_id');
        });
    }

    public function down(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table): void {
            $table->dropColumn('subcategory_ids');
        });
    }
};
