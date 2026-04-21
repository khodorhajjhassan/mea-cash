<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('admin_settings')->updateOrInsert(
            ['key' => 'support_report_delay_hours'],
            [
                'value' => '4',
                'group' => 'support',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('admin_settings')->where('key', 'support_report_delay_hours')->delete();
    }
};
