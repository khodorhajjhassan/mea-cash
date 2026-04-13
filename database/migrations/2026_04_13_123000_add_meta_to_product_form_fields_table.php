<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_form_fields', function (Blueprint $table): void {
            $table->boolean('is_sensitive')->default(false)->after('is_required');
            $table->json('ui_meta')->nullable()->after('validation_rules');
        });
    }

    public function down(): void
    {
        Schema::table('product_form_fields', function (Blueprint $table): void {
            $table->dropColumn(['is_sensitive', 'ui_meta']);
        });
    }
};

