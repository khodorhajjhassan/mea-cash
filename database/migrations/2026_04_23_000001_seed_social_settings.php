<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $keys = [
            'social_facebook'  => ['group' => 'social', 'value' => ''],
            'social_instagram' => ['group' => 'social', 'value' => ''],
            'social_twitter'   => ['group' => 'social', 'value' => ''],
            'social_whatsapp'  => ['group' => 'social', 'value' => ''],
            'social_tiktok'    => ['group' => 'social', 'value' => ''],
        ];

        foreach ($keys as $key => $data) {
            DB::table('admin_settings')->updateOrInsert(
                ['key' => $key],
                ['group' => $data['group'], 'value' => $data['value']],
            );
        }
    }

    public function down(): void
    {
        DB::table('admin_settings')
            ->whereIn('key', ['social_facebook', 'social_instagram', 'social_twitter', 'social_whatsapp', 'social_tiktok'])
            ->delete();
    }
};
