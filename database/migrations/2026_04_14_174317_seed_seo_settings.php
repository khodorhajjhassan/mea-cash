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
        $settings = [
            // Basic Meta
            ['key' => 'seo_title_template', 'value' => '{page_title} — MeaCash', 'group' => 'seo'],
            ['key' => 'seo_title_separator', 'value' => '—', 'group' => 'seo'],
            ['key' => 'seo_default_description', 'value' => 'MeaCash - Your ultimate destination for digital gaming cards and top-ups in Lebanon.', 'group' => 'seo'],
            ['key' => 'seo_default_keywords', 'value' => 'gaming, cards, giftcards, topup, lebanon, meacash', 'group' => 'seo'],
            ['key' => 'seo_canonical_domain', 'value' => 'https://meacash.com', 'group' => 'seo'],
            ['key' => 'seo_robots_default', 'value' => 'index, follow', 'group' => 'seo'],

            // Open Graph
            ['key' => 'og_default_title', 'value' => 'MeaCash Lebanon', 'group' => 'seo'],
            ['key' => 'og_default_description', 'value' => 'Buy gaming cards and top-up your favorite games instantly with MeaCash.', 'group' => 'seo'],
            ['key' => 'og_default_image', 'value' => null, 'group' => 'seo'],
            ['key' => 'og_site_name', 'value' => 'MeaCash', 'group' => 'seo'],
            ['key' => 'og_locale_ar', 'value' => 'ar_LB', 'group' => 'seo'],
            ['key' => 'og_locale_en', 'value' => 'en_US', 'group' => 'seo'],

            // Twitter
            ['key' => 'twitter_card_type', 'value' => 'summary_large_image', 'group' => 'seo'],
            ['key' => 'twitter_site_handle', 'value' => '@meacash', 'group' => 'seo'],
            ['key' => 'twitter_default_title', 'value' => 'MeaCash Lebanon', 'group' => 'seo'],
            ['key' => 'twitter_default_description', 'value' => 'Instant delivery for all your gaming needs.', 'group' => 'seo'],

            // Google
            ['key' => 'google_analytics_id', 'value' => null, 'group' => 'seo'],
            ['key' => 'google_tag_manager_id', 'value' => null, 'group' => 'seo'],
            ['key' => 'google_site_verification', 'value' => null, 'group' => 'seo'],
            ['key' => 'googlebot_directive', 'value' => 'index, follow', 'group' => 'seo'],

            // Social & Pixels
            ['key' => 'facebook_pixel_id', 'value' => null, 'group' => 'seo'],
            ['key' => 'facebook_domain_verification', 'value' => null, 'group' => 'seo'],
            ['key' => 'tiktok_pixel_id', 'value' => null, 'group' => 'seo'],
            ['key' => 'snapchat_pixel_id', 'value' => null, 'group' => 'seo'],

            // Structured Data
            ['key' => 'schema_business_name', 'value' => 'MeaCash', 'group' => 'seo'],
            ['key' => 'schema_business_type', 'value' => 'OnlineStore', 'group' => 'seo'],
            ['key' => 'schema_logo_url', 'value' => null, 'group' => 'seo'],
            ['key' => 'schema_country', 'value' => 'LB', 'group' => 'seo'],
            ['key' => 'schema_currency', 'value' => 'USD', 'group' => 'seo'],

            // Technical
            ['key' => 'sitemap_enabled', 'value' => '1', 'group' => 'seo'],
            ['key' => 'hreflang_enabled', 'value' => '1', 'group' => 'seo'],
            ['key' => 'breadcrumb_schema_enabled', 'value' => '1', 'group' => 'seo'],
        ];

        foreach ($settings as $setting) {
            DB::table('admin_settings')->updateOrInsert(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group'], 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('admin_settings')->where('group', 'seo')->delete();
    }
};
