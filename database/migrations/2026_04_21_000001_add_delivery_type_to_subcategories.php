<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subcategories', function (Blueprint $table): void {
            $table->enum('delivery_type', ['instant', 'fast', 'timed', 'slow'])
                ->default('instant')
                ->after('product_type_id')
                ->index();
            $table->unsignedInteger('delivery_time_minutes')->nullable()->after('delivery_type');
        });
    }

    public function down(): void
    {
        Schema::table('subcategories', function (Blueprint $table): void {
            $table->dropColumn(['delivery_type', 'delivery_time_minutes']);
        });
    }
};
