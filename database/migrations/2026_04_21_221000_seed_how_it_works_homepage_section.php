<?php

use App\Models\HomepageSection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('homepage_sections')) {
            return;
        }

        $exists = DB::table('homepage_sections')
            ->where('type', HomepageSection::TYPE_HOW_IT_WORKS)
            ->where('title_en', 'How It Works')
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('homepage_sections')->insert([
            'type' => HomepageSection::TYPE_HOW_IT_WORKS,
            'source_type' => HomepageSection::SOURCE_CONTENT_BLOCK,
            'title_en' => 'How It Works',
            'title_ar' => 'كيف يعمل',
            'subtitle_en' => 'Choose your product, pay securely, and receive your digital order fast.',
            'subtitle_ar' => 'اختر المنتج، ادفع بأمان، واستلم طلبك الرقمي بسرعة.',
            'settings' => json_encode([
                'features' => [
                    [
                        'icon' => 'search_insights',
                        'label_en' => 'Choose Product',
                        'label_ar' => 'اختر المنتج',
                        'text_en' => 'Browse categories, search brands, and select the package that fits your account or wallet.',
                        'text_ar' => 'تصفح التصنيفات وابحث عن العلامات واختر الباقة المناسبة لحسابك أو محفظتك.',
                    ],
                    [
                        'icon' => 'account_balance_wallet',
                        'label_en' => 'Pay Securely',
                        'label_ar' => 'ادفع بأمان',
                        'text_en' => 'Use your MeaCash wallet or the available payment methods configured for your store.',
                        'text_ar' => 'استخدم محفظة MeaCash أو طرق الدفع المتاحة في المتجر.',
                    ],
                    [
                        'icon' => 'bolt',
                        'label_en' => 'Receive Fast',
                        'label_ar' => 'استلم بسرعة',
                        'text_en' => 'Track the order from your dashboard and receive codes or top-up fulfillment after processing.',
                        'text_ar' => 'تابع الطلب من لوحة حسابك واستلم الأكواد أو الشحن بعد المعالجة.',
                    ],
                ],
            ]),
            'limit' => 1,
            'sort_order' => 80,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('homepage_sections')) {
            return;
        }

        DB::table('homepage_sections')
            ->where('type', HomepageSection::TYPE_HOW_IT_WORKS)
            ->where('title_en', 'How It Works')
            ->delete();
    }
};
