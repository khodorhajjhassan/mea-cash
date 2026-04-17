<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductFormField;
use App\Models\ProductPackage;
use App\Models\ProductType;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StorefrontDemoSeeder extends Seeder
{
    public function run(): void
    {
        // --- Categories ---
        $gaming = Category::create([
            'name_en' => 'Gaming',
            'name_ar' => 'ألعاب',
            'slug' => 'gaming',
            'icon' => '🎮',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $streaming = Category::create([
            'name_en' => 'Streaming',
            'name_ar' => 'بث مباشر',
            'slug' => 'streaming',
            'icon' => '📺',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $social = Category::create([
            'name_en' => 'Social Media',
            'name_ar' => 'تواصل اجتماعي',
            'slug' => 'social-media',
            'icon' => '💬',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $software = Category::create([
            'name_en' => 'Software',
            'name_ar' => 'برامج',
            'slug' => 'software',
            'icon' => '💻',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        $giftCards = Category::create([
            'name_en' => 'Gift Cards',
            'name_ar' => 'بطاقات هدايا',
            'slug' => 'gift-cards',
            'icon' => '🎁',
            'is_active' => true,
            'sort_order' => 5,
        ]);

        // --- Product Type Templates ---
        $gameTopupTemplate = ProductType::create([
            'name' => 'Game Account Top-up',
            'key' => 'game-topup',
            'description' => 'Template for mobile game top-ups with dual form options (Player ID or Login)',
            'is_active' => true,
            'schema' => [
                'fields' => [], // no global fields
                'forms' => [
                    [
                        'key' => 'by-id',
                        'label_en' => 'By Player ID',
                        'label_ar' => 'عبر معرّف اللاعب',
                        'is_default' => true,
                        'sort_order' => 1,
                        'fields' => [
                            ['key' => 'player-id', 'label_en' => 'Player ID', 'label_ar' => 'معرّف اللاعب', 'type' => 'text', 'required' => true, 'placeholder_en' => 'Enter your Player ID', 'placeholder_ar' => 'أدخل معرّف اللاعب', 'sort_order' => 1],
                            ['key' => 'server', 'label_en' => 'Server', 'label_ar' => 'السيرفر', 'type' => 'select', 'required' => true, 'placeholder_en' => 'Select server', 'placeholder_ar' => 'اختر السيرفر', 'sort_order' => 2],
                        ],
                    ],
                    [
                        'key' => 'by-login',
                        'label_en' => 'By Login',
                        'label_ar' => 'عبر تسجيل الدخول',
                        'is_default' => false,
                        'sort_order' => 2,
                        'fields' => [
                            ['key' => 'account-email', 'label_en' => 'Account Email', 'label_ar' => 'بريد الحساب', 'type' => 'email', 'required' => true, 'placeholder_en' => 'your@email.com', 'placeholder_ar' => 'بريدك@email.com', 'sort_order' => 1],
                            ['key' => 'account-password', 'label_en' => 'Account Password', 'label_ar' => 'كلمة مرور الحساب', 'type' => 'password', 'required' => true, 'placeholder_en' => 'Account password', 'placeholder_ar' => 'كلمة المرور', 'sort_order' => 2],
                        ],
                    ],
                ],
            ],
        ]);

        $gameDirectTemplate = ProductType::create([
            'name' => 'Game Direct Charge',
            'key' => 'game-direct',
            'description' => 'Template for direct charge games with single Player ID field',
            'is_active' => true,
            'schema' => [
                'fields' => [
                    ['key' => 'player-id', 'label_en' => 'Player ID', 'label_ar' => 'معرّف اللاعب', 'type' => 'text', 'required' => true, 'placeholder_en' => 'Enter your Player ID', 'placeholder_ar' => 'أدخل معرّف اللاعب', 'sort_order' => 1],
                ],
                'forms' => [],
            ],
        ]);

        // --- Subcategories ---
        $ps = Subcategory::create(['category_id' => $gaming->id, 'name_en' => 'PlayStation', 'name_ar' => 'بلايستيشن', 'slug' => 'playstation', 'is_active' => true, 'is_featured' => true, 'sort_order' => 1]);
        $xbox = Subcategory::create(['category_id' => $gaming->id, 'name_en' => 'Xbox', 'name_ar' => 'اكسبوكس', 'slug' => 'xbox', 'is_active' => true, 'is_featured' => true, 'sort_order' => 2]);
        $steam = Subcategory::create(['category_id' => $gaming->id, 'name_en' => 'Steam', 'name_ar' => 'ستيم', 'slug' => 'steam', 'is_active' => true, 'is_featured' => true, 'sort_order' => 3]);

        // Mobile games subcategories linked to templates
        $mobileGames = Subcategory::create(['category_id' => $gaming->id, 'product_type_id' => $gameTopupTemplate->id, 'name_en' => 'PUBG Mobile', 'name_ar' => 'ببجي موبايل', 'slug' => 'pubg-mobile', 'is_active' => true, 'is_featured' => true, 'sort_order' => 4]);
        $battleRoyale = Subcategory::create(['category_id' => $gaming->id, 'product_type_id' => $gameDirectTemplate->id, 'name_en' => 'Free Fire', 'name_ar' => 'فري فاير', 'slug' => 'free-fire', 'is_active' => true, 'is_featured' => true, 'sort_order' => 5]);

        $netflix = Subcategory::create(['category_id' => $streaming->id, 'name_en' => 'Netflix', 'name_ar' => 'نتفلكس', 'slug' => 'netflix', 'is_active' => true, 'is_featured' => true, 'sort_order' => 1]);
        $spotify = Subcategory::create(['category_id' => $streaming->id, 'name_en' => 'Spotify', 'name_ar' => 'سبوتيفاي', 'slug' => 'spotify', 'is_active' => true, 'is_featured' => true, 'sort_order' => 2]);
        $discord = Subcategory::create(['category_id' => $social->id, 'name_en' => 'Discord', 'name_ar' => 'ديسكورد', 'slug' => 'discord', 'is_active' => true, 'is_featured' => false, 'sort_order' => 1]);
        $adobe = Subcategory::create(['category_id' => $software->id, 'name_en' => 'Adobe', 'name_ar' => 'أدوبي', 'slug' => 'adobe', 'is_active' => true, 'is_featured' => false, 'sort_order' => 1]);
        $amazon = Subcategory::create(['category_id' => $giftCards->id, 'name_en' => 'Amazon', 'name_ar' => 'أمازون', 'slug' => 'amazon', 'is_active' => true, 'is_featured' => true, 'sort_order' => 1]);
        $apple = Subcategory::create(['category_id' => $giftCards->id, 'name_en' => 'Apple', 'name_ar' => 'آبل', 'slug' => 'apple', 'is_active' => true, 'is_featured' => true, 'sort_order' => 2]);

        // --- Products with Packages ---

        // PlayStation
        $psPlus = Product::create([
            'subcategory_id' => $ps->id, 'name_en' => 'PlayStation Plus Subscription', 'name_ar' => 'اشتراك بلايستيشن بلس',
            'slug' => 'ps-plus', 'product_type' => 'fixed_package', 'delivery_type' => 'instant',
            'description_en' => 'Get PlayStation Plus and enjoy online multiplayer, free monthly games, and exclusive discounts.',
            'description_ar' => 'احصل على بلايستيشن بلس واستمتع باللعب الجماعي وألعاب مجانية شهرية وخصومات حصرية.',
            'cost_price' => 8, 'selling_price' => 10, 'is_active' => true, 'is_featured' => true, 'sort_order' => 1,
        ]);
        ProductPackage::create(['product_id' => $psPlus->id, 'name_en' => '1 Month', 'name_ar' => 'شهر واحد', 'amount' => 1, 'cost_price' => 8, 'selling_price' => 10, 'is_available' => true, 'sort_order' => 1]);
        ProductPackage::create(['product_id' => $psPlus->id, 'name_en' => '3 Months', 'name_ar' => '3 أشهر', 'amount' => 3, 'cost_price' => 20, 'selling_price' => 25, 'badge_text' => 'Popular', 'is_available' => true, 'sort_order' => 2]);
        ProductPackage::create(['product_id' => $psPlus->id, 'name_en' => '12 Months', 'name_ar' => '12 شهر', 'amount' => 12, 'cost_price' => 45, 'selling_price' => 55, 'badge_text' => 'Best Value', 'is_available' => true, 'sort_order' => 3]);

        // PSN Card
        $psnCard = Product::create([
            'subcategory_id' => $ps->id, 'name_en' => 'PSN Gift Card (US)', 'name_ar' => 'بطاقة PSN (أمريكي)',
            'slug' => 'psn-gift-card-us', 'product_type' => 'fixed_package', 'delivery_type' => 'instant',
            'description_en' => 'Add funds to your PSN wallet. Valid for US accounts only.',
            'description_ar' => 'أضف رصيد لمحفظة PSN الخاصة بك. صالحة للحسابات الأمريكية فقط.',
            'cost_price' => 9, 'selling_price' => 11, 'is_active' => true, 'is_featured' => false, 'sort_order' => 2,
        ]);
        ProductPackage::create(['product_id' => $psnCard->id, 'name_en' => '$10', 'name_ar' => '10$', 'amount' => 10, 'cost_price' => 9, 'selling_price' => 11, 'is_available' => true, 'sort_order' => 1]);
        ProductPackage::create(['product_id' => $psnCard->id, 'name_en' => '$25', 'name_ar' => '25$', 'amount' => 25, 'cost_price' => 22, 'selling_price' => 27, 'is_available' => true, 'sort_order' => 2]);
        ProductPackage::create(['product_id' => $psnCard->id, 'name_en' => '$50', 'name_ar' => '50$', 'amount' => 50, 'cost_price' => 44, 'selling_price' => 53, 'badge_text' => 'Popular', 'is_available' => true, 'sort_order' => 3]);

        // Xbox
        Product::create([
            'subcategory_id' => $xbox->id, 'name_en' => 'Xbox Game Pass Ultimate', 'name_ar' => 'اكسبوكس غيم باس ألتمت',
            'slug' => 'xbox-game-pass', 'product_type' => 'fixed_package', 'delivery_type' => 'instant',
            'description_en' => 'Access hundreds of games on Xbox and PC with Xbox Game Pass Ultimate.',
            'description_ar' => 'الوصول لمئات الألعاب على اكسبوكس والكمبيوتر مع غيم باس ألتمت.',
            'cost_price' => 12, 'selling_price' => 15, 'is_active' => true, 'is_featured' => true, 'sort_order' => 1,
        ]);

        // Steam
        $steamCard = Product::create([
            'subcategory_id' => $steam->id, 'name_en' => 'Steam Wallet Card', 'name_ar' => 'بطاقة محفظة ستيم',
            'slug' => 'steam-wallet', 'product_type' => 'fixed_package', 'delivery_type' => 'instant',
            'description_en' => 'Add funds to your Steam wallet for games, DLC, and in-game items.',
            'description_ar' => 'أضف رصيد لمحفظة ستيم لشراء الألعاب والمحتوى الإضافي.',
            'cost_price' => 9, 'selling_price' => 11, 'is_active' => true, 'is_featured' => false, 'sort_order' => 1,
        ]);
        ProductPackage::create(['product_id' => $steamCard->id, 'name_en' => '$5', 'name_ar' => '5$', 'amount' => 5, 'cost_price' => 4.50, 'selling_price' => 5.75, 'is_available' => true, 'sort_order' => 1]);
        ProductPackage::create(['product_id' => $steamCard->id, 'name_en' => '$20', 'name_ar' => '20$', 'amount' => 20, 'cost_price' => 18, 'selling_price' => 22, 'is_available' => true, 'sort_order' => 2]);
        ProductPackage::create(['product_id' => $steamCard->id, 'name_en' => '$50', 'name_ar' => '50$', 'amount' => 50, 'cost_price' => 44, 'selling_price' => 53, 'badge_text' => 'Best Value', 'is_available' => true, 'sort_order' => 3]);

        // ==========================================
        // PUBG Mobile UC — account_topup + multi-form
        // ==========================================
        $pubg = Product::create([
            'subcategory_id' => $mobileGames->id,
            'product_type_id' => $gameTopupTemplate->id,
            'name_en' => 'PUBG Mobile UC',
            'name_ar' => 'شدات ببجي موبايل',
            'slug' => 'pubg-mobile-uc',
            'product_type' => 'account_topup',
            'delivery_type' => 'manual',
            'description_en' => 'Top up your PUBG Mobile account with UC. Choose your package and provide your Player ID or login credentials.',
            'description_ar' => 'اشحن حساب ببجي موبايل بالشدات. اختر الباقة وقدّم معرّف اللاعب أو بيانات الدخول.',
            'cost_price' => 1, 'selling_price' => 1.50,
            'is_active' => true, 'is_featured' => true, 'sort_order' => 1,
        ]);
        ProductPackage::create(['product_id' => $pubg->id, 'name_en' => '60 UC', 'name_ar' => '60 شدة', 'amount' => 60, 'cost_price' => 1, 'selling_price' => 1.50, 'is_available' => true, 'sort_order' => 1]);
        ProductPackage::create(['product_id' => $pubg->id, 'name_en' => '325 UC', 'name_ar' => '325 شدة', 'amount' => 325, 'cost_price' => 4.50, 'selling_price' => 5.50, 'badge_text' => 'Popular', 'is_available' => true, 'sort_order' => 2]);
        ProductPackage::create(['product_id' => $pubg->id, 'name_en' => '660 UC', 'name_ar' => '660 شدة', 'amount' => 660, 'cost_price' => 9, 'selling_price' => 11, 'is_available' => true, 'sort_order' => 3]);
        ProductPackage::create(['product_id' => $pubg->id, 'name_en' => '1800 UC', 'name_ar' => '1800 شدة', 'amount' => 1800, 'cost_price' => 24, 'selling_price' => 29, 'badge_text' => 'Best Value', 'is_available' => true, 'sort_order' => 4]);

        // Create form fields manually with proper ui_meta for multi-form tabs
        // Form 1: By Player ID
        ProductFormField::create([
            'product_id' => $pubg->id, 'field_key' => 'by-id__player-id',
            'label_en' => 'Player ID', 'label_ar' => 'معرّف اللاعب',
            'field_type' => 'text', 'is_required' => true,
            'placeholder_en' => 'Enter your Player ID', 'placeholder_ar' => 'أدخل معرّف اللاعب',
            'sort_order' => 1, 'validation_rules' => ['required'],
            'ui_meta' => ['form_key' => 'by-id', 'form_label_en' => 'By Player ID', 'form_label_ar' => 'عبر معرّف اللاعب', 'is_default_form' => true, 'raw_field_key' => 'player-id'],
        ]);
        ProductFormField::create([
            'product_id' => $pubg->id, 'field_key' => 'by-id__server',
            'label_en' => 'Server', 'label_ar' => 'السيرفر',
            'field_type' => 'select', 'is_required' => true,
            'placeholder_en' => 'Select server', 'placeholder_ar' => 'اختر السيرفر',
            'sort_order' => 2, 'validation_rules' => ['required'],
            'ui_meta' => ['form_key' => 'by-id', 'form_label_en' => 'By Player ID', 'form_label_ar' => 'عبر معرّف اللاعب', 'is_default_form' => true, 'raw_field_key' => 'server', 'options' => ['Global', 'Asia', 'Europe', 'North America', 'South America', 'MENA']],
        ]);
        // Form 2: By Login
        ProductFormField::create([
            'product_id' => $pubg->id, 'field_key' => 'by-login__account-email',
            'label_en' => 'Account Email', 'label_ar' => 'بريد الحساب',
            'field_type' => 'email', 'is_required' => true,
            'placeholder_en' => 'your@email.com', 'placeholder_ar' => 'بريدك@email.com',
            'sort_order' => 3, 'validation_rules' => ['required', 'email'],
            'ui_meta' => ['form_key' => 'by-login', 'form_label_en' => 'By Login', 'form_label_ar' => 'عبر تسجيل الدخول', 'is_default_form' => false, 'raw_field_key' => 'account-email'],
        ]);
        ProductFormField::create([
            'product_id' => $pubg->id, 'field_key' => 'by-login__account-password',
            'label_en' => 'Account Password', 'label_ar' => 'كلمة مرور الحساب',
            'field_type' => 'password', 'is_required' => true,
            'placeholder_en' => 'Account password', 'placeholder_ar' => 'كلمة المرور',
            'sort_order' => 4, 'validation_rules' => ['required'],
            'ui_meta' => ['form_key' => 'by-login', 'form_label_en' => 'By Login', 'form_label_ar' => 'عبر تسجيل الدخول', 'is_default_form' => false, 'raw_field_key' => 'account-password'],
        ]);

        // ==========================================
        // Free Fire Diamonds — custom_quantity + single form
        // ==========================================
        $freefire = Product::create([
            'subcategory_id' => $battleRoyale->id,
            'product_type_id' => $gameDirectTemplate->id,
            'name_en' => 'Free Fire Diamonds',
            'name_ar' => 'جواهر فري فاير',
            'slug' => 'free-fire-diamonds',
            'product_type' => 'custom_quantity',
            'delivery_type' => 'manual',
            'description_en' => 'Top up Free Fire Diamonds directly to your account. Enter the amount you want.',
            'description_ar' => 'اشحن جواهر فري فاير مباشرة لحسابك. أدخل الكمية التي تريدها.',
            'cost_price' => 0.008, 'selling_price' => 0.01,
            'price_per_unit' => 0.01,
            'min_quantity' => 100,
            'max_quantity' => 10000,
            'is_active' => true, 'is_featured' => true, 'sort_order' => 1,
        ]);
        // Single form field: Player ID
        ProductFormField::create([
            'product_id' => $freefire->id, 'field_key' => 'player-id',
            'label_en' => 'Player ID', 'label_ar' => 'معرّف اللاعب',
            'field_type' => 'text', 'is_required' => true,
            'placeholder_en' => 'Enter your Free Fire Player ID', 'placeholder_ar' => 'أدخل معرّف لاعب فري فاير',
            'sort_order' => 1, 'validation_rules' => ['required'],
            'ui_meta' => ['form_key' => null, 'form_label_en' => null, 'form_label_ar' => null, 'is_default_form' => false, 'raw_field_key' => 'player-id'],
        ]);

        // Netflix
        Product::create([
            'subcategory_id' => $netflix->id, 'name_en' => 'Netflix Premium Account', 'name_ar' => 'حساب نتفلكس بريميوم',
            'slug' => 'netflix-premium', 'product_type' => 'fixed_package', 'delivery_type' => 'instant',
            'description_en' => 'Netflix Premium account with 4K Ultra HD, watch on 4 screens simultaneously.',
            'description_ar' => 'حساب نتفلكس بريميوم بدقة 4K، مشاهدة على 4 شاشات في نفس الوقت.',
            'cost_price' => 3, 'selling_price' => 5, 'is_active' => true, 'is_featured' => true, 'sort_order' => 1,
        ]);

        // Spotify
        Product::create([
            'subcategory_id' => $spotify->id, 'name_en' => 'Spotify Premium', 'name_ar' => 'سبوتيفاي بريميوم',
            'slug' => 'spotify-premium', 'product_type' => 'fixed_package', 'delivery_type' => 'instant',
            'description_en' => 'Spotify Premium subscription — ad-free music, offline downloads, high quality audio.',
            'description_ar' => 'اشتراك سبوتيفاي بريميوم - موسيقى بدون إعلانات، تحميل بدون انترنت.',
            'cost_price' => 2, 'selling_price' => 3.50, 'is_active' => true, 'is_featured' => true, 'sort_order' => 1,
        ]);

        // Discord Nitro
        Product::create([
            'subcategory_id' => $discord->id, 'name_en' => 'Discord Nitro', 'name_ar' => 'ديسكورد نيترو',
            'slug' => 'discord-nitro', 'product_type' => 'fixed_package', 'delivery_type' => 'instant',
            'description_en' => 'Discord Nitro with HD video, custom emojis, bigger uploads, and server boost.',
            'description_ar' => 'ديسكورد نيترو مع فيديو عالي الجودة، إيموجي مخصصة، وتعزيز السيرفر.',
            'cost_price' => 8, 'selling_price' => 10, 'is_active' => true, 'is_featured' => false, 'sort_order' => 1,
        ]);

        // Adobe
        Product::create([
            'subcategory_id' => $adobe->id, 'name_en' => 'Adobe Creative Cloud', 'name_ar' => 'أدوبي كرييتف كلاود',
            'slug' => 'adobe-cc', 'product_type' => 'fixed_package', 'delivery_type' => 'instant',
            'description_en' => 'Full Adobe Creative Cloud access: Photoshop, Illustrator, Premiere Pro, and more.',
            'description_ar' => 'وصول كامل لأدوبي كرييتف كلاود: فوتوشوب، إلستريتر، بريمير برو والمزيد.',
            'cost_price' => 5, 'selling_price' => 8, 'is_active' => true, 'is_featured' => false, 'sort_order' => 1,
        ]);

        // Amazon
        $amazonCard = Product::create([
            'subcategory_id' => $amazon->id, 'name_en' => 'Amazon Gift Card', 'name_ar' => 'بطاقة هدية أمازون',
            'slug' => 'amazon-gift-card', 'product_type' => 'fixed_package', 'delivery_type' => 'instant',
            'description_en' => 'Amazon gift card — valid for amazon.com purchases.',
            'description_ar' => 'بطاقة هدية أمازون - صالحة للمشتريات على amazon.com.',
            'cost_price' => 9, 'selling_price' => 11, 'is_active' => true, 'is_featured' => true, 'sort_order' => 1,
        ]);
        ProductPackage::create(['product_id' => $amazonCard->id, 'name_en' => '$10', 'name_ar' => '10$', 'amount' => 10, 'cost_price' => 9, 'selling_price' => 11, 'is_available' => true, 'sort_order' => 1]);
        ProductPackage::create(['product_id' => $amazonCard->id, 'name_en' => '$25', 'name_ar' => '25$', 'amount' => 25, 'cost_price' => 22, 'selling_price' => 27, 'is_available' => true, 'sort_order' => 2]);
        ProductPackage::create(['product_id' => $amazonCard->id, 'name_en' => '$50', 'name_ar' => '50$', 'amount' => 50, 'cost_price' => 44, 'selling_price' => 53, 'is_available' => true, 'sort_order' => 3]);
        ProductPackage::create(['product_id' => $amazonCard->id, 'name_en' => '$100', 'name_ar' => '100$', 'amount' => 100, 'cost_price' => 88, 'selling_price' => 105, 'badge_text' => 'Best Deal', 'is_available' => true, 'sort_order' => 4]);

        // Apple
        Product::create([
            'subcategory_id' => $apple->id, 'name_en' => 'Apple iTunes Gift Card', 'name_ar' => 'بطاقة آيتونز',
            'slug' => 'itunes-gift-card', 'product_type' => 'fixed_package', 'delivery_type' => 'instant',
            'description_en' => 'Apple iTunes/App Store gift card for apps, music, movies, and more.',
            'description_ar' => 'بطاقة آيتونز/آب ستور لشراء التطبيقات والموسيقى والأفلام.',
            'cost_price' => 9, 'selling_price' => 11, 'is_active' => true, 'is_featured' => false, 'sort_order' => 1,
        ]);

        // --- Admin User ---
        $adminEmail = strtolower((string) env('SUPER_ADMIN_EMAIL', 'admin@meacash.com'));
        $adminPassword = (string) env('SUPER_ADMIN_PASSWORD', 'password');
        User::query()->updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Admin',
                'password' => Hash::make($adminPassword),
                'is_admin' => true,
                'is_active' => true,
            ]
        );

        // --- Demo Customer ---
        User::create([
            'name' => 'Mohammed',
            'email' => 'test@meacash.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'is_active' => true,
            'preferred_language' => 'en',
        ]);
    }
}
