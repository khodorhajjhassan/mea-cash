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

        $now = now();
        $sections = [
            [
                'type' => HomepageSection::TYPE_TRUST_PAYMENTS,
                'source_type' => HomepageSection::SOURCE_CONTENT_BLOCK,
                'title_en' => 'Why MeaCash?',
                'title_ar' => 'لماذا MeaCash؟',
                'subtitle_en' => 'A faster, clearer way to buy digital cards, game top-ups, and online services with trusted local support.',
                'subtitle_ar' => 'طريقة أسرع وأوضح لشراء البطاقات الرقمية وشحن الألعاب والخدمات الإلكترونية مع دعم محلي موثوق.',
                'settings' => [
                    'badge_en' => 'Trusted Checkout',
                    'badge_ar' => 'دفع موثوق',
                    'features' => [
                        ['icon' => 'bolt', 'label_en' => 'Instant Delivery', 'label_ar' => 'تسليم فوري', 'text_en' => 'Digital codes and top-ups are processed quickly after payment confirmation.', 'text_ar' => 'تتم معالجة الأكواد وعمليات الشحن بسرعة بعد تأكيد الدفع.'],
                        ['icon' => 'account_balance_wallet', 'label_en' => 'Wallet Ready', 'label_ar' => 'محفظة جاهزة', 'text_en' => 'Top up once, then use your balance for smoother repeat orders.', 'text_ar' => 'اشحن محفظتك مرة واحدة واستخدم الرصيد لطلبات أسرع.'],
                        ['icon' => 'shield', 'label_en' => 'Verified Products', 'label_ar' => 'منتجات موثوقة', 'text_en' => 'Cards, packages, and services are managed from approved suppliers.', 'text_ar' => 'تتم إدارة البطاقات والباقات والخدمات من موردين معتمدين.'],
                        ['icon' => 'support_agent', 'label_en' => 'Local Support', 'label_ar' => 'دعم محلي', 'text_en' => 'Our team follows orders, reports, and wallet top-ups when you need help.', 'text_ar' => 'فريقنا يتابع الطلبات والبلاغات وشحن المحفظة عند الحاجة.'],
                    ],
                ],
                'sort_order' => 35,
            ],
            [
                'type' => HomepageSection::TYPE_SHOP_BY_NEED,
                'source_type' => HomepageSection::SOURCE_CONTENT_BLOCK,
                'title_en' => 'Shop By Need',
                'title_ar' => 'تسوق حسب حاجتك',
                'subtitle_en' => 'Jump straight to the products customers ask for most.',
                'subtitle_ar' => 'انتقل مباشرة إلى المنتجات الأكثر طلباً.',
                'settings' => [
                    'badge_en' => 'Marketplace',
                    'badge_ar' => 'المتجر',
                    'cards' => [
                        ['icon' => 'card_giftcard', 'accent' => '#00e5ff', 'title_en' => 'Gift Cards', 'title_ar' => 'بطاقات الهدايا', 'text_en' => 'Popular digital cards for shopping, streaming, and apps.', 'text_ar' => 'بطاقات رقمية للتسوق والبث والتطبيقات.', 'url' => '#products-section'],
                        ['icon' => 'sports_esports', 'accent' => '#fe00fe', 'title_en' => 'Game Top-Ups', 'title_ar' => 'شحن الألعاب', 'text_en' => 'PUBG, Free Fire, PlayStation, Xbox, and more.', 'text_ar' => 'PUBG وFree Fire وPlayStation وXbox والمزيد.', 'url' => '#products-section'],
                        ['icon' => 'subscriptions', 'accent' => '#fbbf24', 'title_en' => 'Subscriptions', 'title_ar' => 'الاشتراكات', 'text_en' => 'Entertainment, software, and premium account services.', 'text_ar' => 'خدمات الترفيه والبرامج والحسابات المميزة.', 'url' => '#products-section'],
                        ['icon' => 'phone_iphone', 'accent' => '#22c55e', 'title_en' => 'Mobile Recharge', 'title_ar' => 'شحن الهاتف', 'text_en' => 'Fast recharge experiences for prepaid and digital services.', 'text_ar' => 'تجربة شحن سريعة للخطوط والخدمات الرقمية.', 'url' => '#products-section'],
                        ['icon' => 'inventory_2', 'accent' => '#a855f7', 'title_en' => 'Wholesale', 'title_ar' => 'الجملة', 'text_en' => 'Bulk digital products and reseller-friendly support.', 'text_ar' => 'منتجات رقمية بالجملة ودعم مناسب للموزعين.', 'url' => '#products-section'],
                        ['icon' => 'travel_explore', 'accent' => '#38bdf8', 'title_en' => 'Coming Next', 'title_ar' => 'قريباً', 'text_en' => 'eSIM, travel, and more marketplace services.', 'text_ar' => 'eSIM والسفر وخدمات متجر إضافية.', 'url' => '#products-section'],
                    ],
                ],
                'sort_order' => 40,
            ],
            [
                'type' => HomepageSection::TYPE_CRYPTO_CARD,
                'source_type' => HomepageSection::SOURCE_CONTENT_BLOCK,
                'title_en' => 'Spend Crypto Anywhere',
                'title_ar' => 'استخدم الكريبتو بسهولة',
                'subtitle_en' => 'Use crypto-friendly wallet payments, quick balance top-ups, and multi-currency checkout for everyday digital purchases.',
                'subtitle_ar' => 'استخدم مدفوعات المحفظة وشحن الرصيد السريع والدفع بعملات متعددة لشراء المنتجات الرقمية اليومية.',
                'settings' => [
                    'badge_en' => 'Crypto Wallet',
                    'badge_ar' => 'محفظة كريبتو',
                    'button_text_en' => 'Start shopping',
                    'button_text_ar' => 'ابدأ التسوق',
                    'button_url' => '#products-section',
                    'status_en' => 'Active',
                    'status_ar' => 'نشط',
                    'amount_label_en' => '$2,450',
                    'amount_label_ar' => '$2,450',
                    'card_brand_en' => 'MEACASH CARD',
                    'card_brand_ar' => 'بطاقة MEACASH',
                    'card_kind_en' => 'CRYPTO WALLET',
                    'card_kind_ar' => 'محفظة كريبتو',
                    'card_holder_en' => 'MEACASH USER',
                    'card_holder_ar' => 'مستخدم MEACASH',
                    'features' => [
                        ['icon' => 'shield', 'label_en' => 'Secure', 'label_ar' => 'آمن'],
                        ['icon' => 'bolt', 'label_en' => 'Instant Top-Up', 'label_ar' => 'شحن فوري'],
                        ['icon' => 'public', 'label_en' => 'Global', 'label_ar' => 'عالمي'],
                        ['icon' => 'currency_exchange', 'label_en' => 'Multi-Currency', 'label_ar' => 'عملات متعددة'],
                    ],
                ],
                'sort_order' => 45,
            ],
        ];

        foreach ($sections as $section) {
            $exists = DB::table('homepage_sections')
                ->where('type', $section['type'])
                ->where('title_en', $section['title_en'])
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('homepage_sections')->insert([
                ...$section,
                'settings' => json_encode($section['settings']),
                'limit' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('homepage_sections')) {
            return;
        }

        DB::table('homepage_sections')
            ->whereIn('type', [
                HomepageSection::TYPE_TRUST_PAYMENTS,
                HomepageSection::TYPE_SHOP_BY_NEED,
                HomepageSection::TYPE_CRYPTO_CARD,
            ])
            ->whereIn('title_en', ['Why MeaCash?', 'Shop By Need', 'Spend Crypto Anywhere'])
            ->delete();
    }
};
