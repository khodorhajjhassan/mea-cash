<?php

namespace Database\Seeders;

use App\Models\AdminSetting;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Feedback;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductCode;
use App\Models\ProductFormField;
use App\Models\ProductPackage;
use App\Models\ProductType;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\TopupRequest;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Banner;
use App\Models\Faq;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create();
        $adminEmail = strtolower((string) env('SUPER_ADMIN_EMAIL', 'admin@meacash.com'));
        $adminPassword = (string) env('SUPER_ADMIN_PASSWORD', 'password');

        $admin = User::query()->updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'MeaCash Admin',
                'phone' => '70000000',
                'password' => Hash::make($adminPassword),
                'preferred_language' => 'en',
                'is_active' => true,
                'is_admin' => true,
            ]
        );

        Wallet::query()->firstOrCreate(
            ['user_id' => $admin->id],
            ['balance' => 0, 'currency' => 'USD']
        );

        // ─── Suppliers ───
        $suppliers = collect([
            ['name' => 'Global Pins Hub', 'contact_name' => 'Nora Ali', 'email' => 'sales@globalpins.example', 'phone' => '71110001', 'notes' => 'Primary code supplier', 'is_active' => true],
            ['name' => 'TopUp Express', 'contact_name' => 'Rami S', 'email' => 'ops@topupexpress.example', 'phone' => '71110002', 'notes' => 'Fast delivery for gaming', 'is_active' => true],
            ['name' => 'Digital Wallet Source', 'contact_name' => 'Mina K', 'email' => 'support@dwsource.example', 'phone' => '71110003', 'notes' => 'Flexible payment products', 'is_active' => true],
            ['name' => 'GiftCard Depot', 'contact_name' => 'Rana F', 'email' => 'team@giftcarddepot.example', 'phone' => '71110004', 'notes' => null, 'is_active' => true],
        ])->map(fn(array $data) => Supplier::query()->updateOrCreate(['name' => $data['name']], $data));

        // ─── Product Types ───
        $productTypes = collect([
            [
                'name' => 'Account ID Required',
                'key' => 'account-id-required',
                'description' => 'Requires one account ID input before checkout.',
                'is_active' => true,
                'schema' => [
                    'modes' => ['package'],
                    'fields' => [
                        [
                            'key' => 'account_id',
                            'label' => 'Account ID',
                            'type' => 'text',
                            'required' => true,
                            'placeholder' => 'Enter your account ID',
                            'rules' => ['required', 'string', 'max:120'],
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Account + Login Credentials',
                'key' => 'account-login-credentials',
                'description' => 'Requires account ID, login email, and password.',
                'is_active' => true,
                'schema' => [
                    'modes' => ['package', 'quantity'],
                    'fields' => [
                        [
                            'key' => 'account_id',
                            'label' => 'Account ID',
                            'type' => 'text',
                            'required' => true,
                            'placeholder' => 'Enter your account ID',
                            'rules' => ['required', 'string', 'max:120'],
                            'sort_order' => 1,
                        ],
                        [
                            'key' => 'email',
                            'label' => 'Login Email',
                            'type' => 'email',
                            'required' => true,
                            'placeholder' => 'Enter your email',
                            'rules' => ['required', 'email'],
                            'sort_order' => 2,
                        ],
                        [
                            'key' => 'password',
                            'label' => 'Password',
                            'type' => 'password',
                            'required' => true,
                            'placeholder' => 'Enter your password',
                            'rules' => ['required', 'string', 'min:6'],
                            'sort_order' => 3,
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Quantity or Price Mode',
                'key' => 'quantity-or-price',
                'description' => 'Supports quantity/price purchase mode with account ID.',
                'is_active' => true,
                'schema' => [
                    'modes' => ['quantity', 'price'],
                    'fields' => [
                        [
                            'key' => 'account_id',
                            'label' => 'Account ID',
                            'type' => 'text',
                            'required' => true,
                            'placeholder' => 'Enter your account ID',
                            'rules' => ['required', 'string', 'max:120'],
                            'sort_order' => 1,
                        ],
                        [
                            'key' => 'quantity',
                            'label' => 'Quantity',
                            'type' => 'number',
                            'required' => true,
                            'placeholder' => 'Enter quantity',
                            'rules' => ['required', 'numeric', 'min:1'],
                            'sort_order' => 2,
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Package Only',
                'key' => 'package-only',
                'description' => 'No additional inputs required, only package selection.',
                'is_active' => true,
                'schema' => [
                    'modes' => ['package'],
                    'fields' => [],
                ],
            ],
        ])->map(function (array $data) {
            return ProductType::query()->updateOrCreate(
                ['key' => $data['key']],
                $data
            );
        })->keyBy('key');

        // ─── Expanded Catalog Map (10+ Categories) ───
        $catalogMap = [
            'Gaming' => [
                'Free Fire' => ['110 Diamonds', '231 Diamonds', '572 Diamonds', '1166 Diamonds'],
                'PUBG Mobile' => ['60 UC', '325 UC', '660 UC', '1800 UC'],
                'Mobile Legends' => ['86 Diamonds', '172 Diamonds', '257 Diamonds', '706 Diamonds'],
                'Clash of Clans' => ['80 Gems', '500 Gems', '1200 Gems', '2500 Gems'],
            ],
            'Social Media' => [
                'TikTok Coins' => ['100 Coins', '350 Coins', '700 Coins', '1400 Coins'],
                'BIGO Live' => ['100 Beans', '500 Beans', '1000 Beans', '5000 Beans'],
                'IMO' => ['100 Credits', '300 Credits', '600 Credits', '1200 Credits'],
            ],
            'Gift Cards' => [
                'PlayStation Store' => ['10 USD', '20 USD', '50 USD', '100 USD'],
                'Xbox Gift Card' => ['10 USD', '25 USD', '50 USD', '100 USD'],
                'Steam Wallet' => ['5 USD', '10 USD', '20 USD', '50 USD'],
                'iTunes US' => ['10 USD', '25 USD', '50 USD', '100 USD'],
            ],
            'Streaming' => [
                'Netflix' => ['1 Month', '3 Months', '6 Months', '12 Months'],
                'Spotify Premium' => ['1 Month', '3 Months', '6 Months', '12 Months'],
                'Shahid VIP' => ['1 Month', '3 Months', '12 Months', 'Sports Pack'],
            ],
            'Software' => [
                'Windows 11 Pro' => ['Retail Key', 'OEM Key'],
                'Office 2021' => ['Global Key'],
                'Adobe Creative Cloud' => ['1 Month', '1 Year'],
            ],
            'Antivirus' => [
                'Kaspersky' => ['1 Device / 1 Year', '3 Devices / 1 Year'],
                'Eset NOD32' => ['1 Year License'],
                'McAfee' => ['Total Protection'],
            ],
            'Education' => [
                'LinkedIn Learning' => ['1 Month', 'Premium Account'],
                'Skillshare' => ['Annual Account'],
                'Udemy' => ['Gift Vouchers'],
            ],
            'Work & Tools' => [
                'Canva Pro' => ['Team Invite', '1 Year Individual'],
                'Zoom Pro' => ['1 Month License'],
                'ChatGPT Plus' => ['Shared Account'],
            ],
            'Mobile Topup' => [
                'Alfa Lebanon' => ['10,000 LBP', '22,000 LBP'],
                'Touch Lebanon' => ['Credits', 'Data Bundle'],
            ],
            'Internet' => [
                'Connect ISP' => ['50GB', '100GB'],
                'Sodetel' => ['Points Card'],
            ],
            'Payments' => [
                'USDT (TRC20)' => ['10 USD', '25 USD', '50 USD', '100 USD'],
                'Perfect Money' => ['10 USD', '25 USD', '50 USD', '100 USD'],
                'Payeer' => ['10 USD', '20 USD', '50 USD', '100 USD'],
            ],
        ];

        $categoryIcons = [
            'Gaming' => '🎮',
            'Social Media' => '📱',
            'Gift Cards' => '🎁',
            'Streaming' => '📺',
            'Software' => '💻',
            'Antivirus' => '🛡️',
            'Education' => '📚',
            'Work & Tools' => '⚙️',
            'Mobile Topup' => '📞',
            'Internet' => '🌐',
            'Payments' => '💳',
        ];

        // ─── Favicon / Brand Logos for placeholders ───
        $brandImages = [
            'canva' => 'https://t3.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=http://canva.com&size=128',
            'amazon' => 'https://t0.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=http://amazon.com&size=128',
            'pubg' => 'https://www.pubgmobile.com/common/images/icon_logo.jpg',
            'freefire' => 'https://ff.garena.com/static/logo.png',
            'mobilelegends' => 'https://m.mobilelegends.com/static/images/favicon.ico',
            'jawaker' => 'https://www.jawaker.com/favicon.ico',
            'razer' => 'https://gold.razer.com/favicon.ico',
            'netflix' => 'https://www.netflix.com/favicon.ico',
            'spotify' => 'https://www.scdn.co/v2/2/5/0/favicon.ico',
            'steam' => 'https://store.steampowered.com/favicon.ico',
            'xbox' => 'https://www.xbox.com/favicon.ico',
            'playstation' => 'https://www.playstation.com/favicon.ico',
            'google' => 'https://www.google.com/favicon.ico',
        ];

        $allProducts = collect();

        foreach ($catalogMap as $categoryName => $subcategoriesData) {
            $category = Category::query()->updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                [
                    'name_en' => $categoryName,
                    'name_ar' => $categoryName, // In real app, provide Arabic translations
                    'icon' => $categoryIcons[$categoryName] ?? '✨',
                    'is_active' => true,
                    'sort_order' => rand(1, 50),
                    'seo_title' => $categoryName . ' Digital Services',
                    'seo_description' => 'Fast and secure ' . $categoryName . ' topups and licenses.',
                ]
            );

            foreach ($subcategoriesData as $subcategoryName => $packages) {
                // Featured logic: Some specific brands are featured, others random
                $featuredBrands = ['PUBG MOBLIE', 'Free Fire', 'Mobile Legends', 'Jawaker', 'Netflix', 'Spotify', 'Steam', 'TikTok Coins', 'Razer Gold'];
                $isFeaturedSub = in_array($subcategoryName, $featuredBrands) || (rand(0, 100) < 15);

                $subcategory = Subcategory::query()->updateOrCreate(
                    ['slug' => Str::slug($subcategoryName)],
                    [
                        'category_id' => $category->id,
                        'name_en' => $subcategoryName,
                        'name_ar' => $subcategoryName,
                        'description_en' => 'Get the best rates for ' . $subcategoryName . ' with instant delivery.',
                        'description_ar' => 'احصل على أفضل الأسعار لـ ' . $subcategoryName . ' مع تسليم فوري.',
                        'is_active' => true,
                        'is_featured' => $isFeaturedSub,
                        'sort_order' => rand(1, 100),
                    ]
                );

                $supplierId = $suppliers->random()->id;
                $isCustom = in_array($subcategoryName, ['BIGO Live', 'TikTok Coins'], true);
                $templateKey = match ($subcategoryName) {
                    'TikTok Coins' => 'account-login-credentials',
                    'BIGO Live' => 'quantity-or-price',
                    'USDT (TRC20)', 'Perfect Money', 'Payeer' => 'package-only',
                    default => 'account-id-required',
                };

                // Assign a brand image if match found
                $imagePath = null;
                $lowerSub = strtolower($subcategoryName);
                if (str_contains($lowerSub, 'canva'))
                    $imagePath = $brandImages['canva'];
                elseif (str_contains($lowerSub, 'amazon'))
                    $imagePath = $brandImages['amazon'];
                elseif (str_contains($lowerSub, 'pubg'))
                    $imagePath = $brandImages['pubg'];
                elseif (str_contains($lowerSub, 'free fire'))
                    $imagePath = $brandImages['freefire'];
                elseif (str_contains($lowerSub, 'mobile legends'))
                    $imagePath = $brandImages['mobilelegends'];
                elseif (str_contains($lowerSub, 'jawaker'))
                    $imagePath = $brandImages['jawaker'];
                elseif (str_contains($lowerSub, 'razer'))
                    $imagePath = $brandImages['razer'];
                elseif (str_contains($lowerSub, 'netflix'))
                    $imagePath = $brandImages['netflix'];
                elseif (str_contains($lowerSub, 'spotify'))
                    $imagePath = $brandImages['spotify'];
                elseif (str_contains($lowerSub, 'steam'))
                    $imagePath = $brandImages['steam'];
                elseif (str_contains($lowerSub, 'xbox'))
                    $imagePath = $brandImages['xbox'];
                elseif (str_contains($lowerSub, 'playstation'))
                    $imagePath = $brandImages['playstation'];

                // Define products for this subcategory
                foreach ($packages as $index => $packageName) {
                    $amount = (float) preg_replace('/[^0-9.]/', '', $packageName) ?: ($index + 1) * 10;
                    $costPrice = max(0.5, $amount * 0.009);
                    $sellPrice = round($costPrice * 1.20, 2);

                    $product = Product::query()->updateOrCreate(
                        ['slug' => Str::slug($subcategoryName . ' ' . $packageName)],
                        [
                            'subcategory_id' => $subcategory->id,
                            'supplier_id' => $supplierId,
                            'product_type_id' => $productTypes->get($templateKey)?->id,
                            'name_en' => $packageName,
                            'name_ar' => $packageName,
                            'description_en' => 'Official recharge service for ' . $subcategoryName . '. 24/7 Support.',
                            'description_ar' => 'خدمة شحن رسمية لـ ' . $subcategoryName . '. دعم على مدار الساعة.',
                            'product_type' => $isCustom ? 'custom_quantity' : 'fixed_package',
                            'delivery_type' => 'instant',
                            'delivery_time_minutes' => 5,
                            'cost_price' => $costPrice,
                            'selling_price' => $sellPrice,
                            'price_per_unit' => $isCustom ? 0.012 : null,
                            'min_quantity' => 1,
                            'max_quantity' => $isCustom ? 1000000 : null,
                            'image' => $imagePath,
                            'is_active' => true,
                            'is_featured' => (bool) (rand(0, 10) < 3), // 30% of products are featured
                            'sort_order' => $index + 1,
                        ]
                    );

                    $allProducts->push($product);
                    $this->seedProductFormFieldsFromTemplate($product);

                    // Add some codes for each product
                    for ($i = 0; $i < 10; $i++) {
                        ProductCode::query()->create([
                            'product_id' => $product->id,
                            'package_id' => null, // No more packages
                            'code' => strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)),
                            'status' => 'available',
                        ]);
                    }
                }
            }
        }

        // ─── Banners ───
        $banners = [
            [
                'title_en' => 'Summer Gaming Fest',
                'title_ar' => 'مهرجان الصيف للألعاب',
                'description_en' => 'Up to 50% discount on all gaming cards.',
                'description_ar' => 'خصم يصل إلى 50٪ على جميع بطاقات الألعاب.',
                'image_path' => 'banners/gaming.webp',
                'link' => '/store?category=gaming',
                'button_text_en' => 'Shop Now',
                'button_text_ar' => 'تسوق الآن',
            ],
            [
                'title_en' => 'Instant Crypto Topups',
                'title_ar' => 'شحن فوري للعملات الرقمية',
                'description_en' => 'Buy USDT and Perfect Money with zero fees.',
                'description_ar' => 'اشترِ USDT و Perfect Money بدون رسوم.',
                'image_path' => 'banners/crypto.webp',
                'link' => '/store?category=payments',
                'button_text_en' => 'Exchange Now',
                'button_text_ar' => 'حول الآن',
            ],
        ];
        foreach ($banners as $b) {
            Banner::updateOrCreate(['title_en' => $b['title_en']], array_merge($b, ['is_active' => true]));
        }

        // ─── FAQs ───
        $faqs = [
            [
                'question_en' => 'How long does delivery take?',
                'question_ar' => 'كم يستغرق التسليم؟',
                'answer_en' => 'Most digital cards are delivered instantly to your account. Some manual services might take 15-30 minutes.',
                'answer_ar' => 'يتم تسليم معظم البطاقات الرقمية فوراً إلى حسابك. قد تستغرق بعض الخدمات اليدوية من 15 إلى 30 دقيقة.',
            ],
            [
                'question_en' => 'What payment methods do you accept?',
                'question_ar' => 'ما هي طرق الدفع المقبولة؟',
                'answer_en' => 'We accept OMT, Wish Money, and various cryptocurrencies like USDT.',
                'answer_ar' => 'نحن نقبل OMT و Wish Money ومختلف العملات الرقمية مثل USDT.',
            ],
            [
                'question_en' => 'Is my payment secure?',
                'question_ar' => 'هل عملية الدفع آمنة؟',
                'answer_en' => 'Yes, all transactions are encrypted and processed through secure local and global gateways.',
                'answer_ar' => 'نعم، جميع المعاملات مشفرة وتتم عبر بوابات محلية وعالمية آمنة.',
            ],
        ];
        foreach ($faqs as $f) {
            Faq::updateOrCreate(['question_en' => $f['question_en']], array_merge($f, ['is_active' => true]));
        }

        // ─── Payment Methods ───
        PaymentMethod::query()->updateOrCreate(['method' => 'omt'], [
            'display_name_ar' => 'OMT',
            'display_name_en' => 'OMT',
            'account_identifier' => '70-123456',
            'instructions_ar' => 'أرسل المبلغ المطلوب ثم أرفق صورة الإيصال أو رقم العملية.',
            'instructions_en' => 'Send the exact amount, then submit the receipt image or transaction ID.',
            'is_active' => true,
        ]);
        PaymentMethod::query()->updateOrCreate(['method' => 'wish'], [
            'display_name_ar' => 'ويش موني',
            'display_name_en' => 'Wish Money',
            'account_identifier' => '70-654321',
            'instructions_ar' => 'أرسل المبلغ المطلوب ثم أرفق صورة الإيصال أو رقم العملية.',
            'instructions_en' => 'Send the exact amount, then submit the receipt image or transaction ID.',
            'is_active' => true,
        ]);
        PaymentMethod::query()->updateOrCreate(['method' => 'usdt'], [
            'display_name_ar' => 'USDT (TRC20)',
            'display_name_en' => 'USDT (TRC20)',
            'account_identifier' => 'TQ7QhFakeAddressDemoSeedOnly',
            'instructions_ar' => 'أرسل USDT على شبكة TRC20 فقط ثم أرفق رقم العملية.',
            'instructions_en' => 'Send USDT on TRC20 only, then submit the transaction ID.',
            'is_active' => true,
        ]);

        // ─── Users & Wallets ───
        for ($i = 1; $i <= 50; $i++) {
            $user = User::query()->updateOrCreate(
                ['email' => 'user' . $i . '@meacash.dev'],
                [
                    'name' => $faker->name(),
                    'phone' => '70' . rand(100000, 999999),
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );
            Wallet::query()->updateOrCreate(['user_id' => $user->id], ['balance' => rand(10, 500)]);
        }

        // ─── Settings ───
        AdminSetting::query()->updateOrCreate(['key' => 'site_name'], ['value' => 'MeaCash', 'group' => 'general']);
        AdminSetting::query()->updateOrCreate(['key' => 'support_email'], ['value' => 'support@meacash.com', 'group' => 'general']);
        AdminSetting::query()->updateOrCreate(['key' => 'page_about'], [
            'value' => '<p>MeaCash is a digital marketplace for game top-ups, gift cards, software, and online services with fast wallet-based checkout.</p>',
            'group' => 'pages',
        ]);
        AdminSetting::query()->updateOrCreate(['key' => 'page_terms'], [
            'value' => '<p>By using MeaCash, customers agree to provide accurate account details, keep their login information secure, and use purchased digital products according to platform rules.</p>',
            'group' => 'pages',
        ]);
        AdminSetting::query()->updateOrCreate(['key' => 'page_privacy'], [
            'value' => '<p>MeaCash collects the information needed to process orders, wallet top-ups, support requests, and account security. We do not sell customer data.</p>',
            'group' => 'pages',
        ]);
        AdminSetting::query()->updateOrCreate(['key' => 'page_refunds'], [
            'value' => '<p>Refunds are reviewed case by case. Completed digital deliveries may not be refundable unless there is a confirmed delivery issue or an admin-approved exception.</p>',
            'group' => 'pages',
        ]);
    }

    private function seedProductFormFieldsFromTemplate(Product $product): void
    {
        $product->loadMissing('productTypeDefinition');
        $schema = $product->productTypeDefinition?->schema;
        $fields = is_array($schema) && array_key_exists('fields', $schema) ? $schema['fields'] : $schema;

        if (!is_array($fields))
            return;

        ProductFormField::query()->where('product_id', $product->id)->delete();

        foreach ($fields as $index => $field) {
            ProductFormField::query()->create([
                'product_id' => $product->id,
                'field_key' => $field['key'] ?? 'field_' . $index,
                'label_en' => $field['label'] ?? Str::headline($field['key']),
                'label_ar' => $field['label'] ?? Str::headline($field['key']),
                'field_type' => $field['type'] ?? 'text',
                'placeholder_en' => $field['placeholder'] ?? '',
                'placeholder_ar' => $field['placeholder'] ?? '',
                'is_required' => $field['required'] ?? false,
                'sort_order' => $field['sort_order'] ?? $index + 1,
                'validation_rules' => $field['rules'] ?? [],
            ]);
        }
    }
}
