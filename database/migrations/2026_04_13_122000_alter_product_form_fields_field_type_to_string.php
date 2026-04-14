<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE product_form_fields ALTER COLUMN field_type TYPE VARCHAR(50) USING field_type::text');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE product_form_fields ALTER COLUMN field_type TYPE VARCHAR(50)");
        }
    }
};

