<?php

namespace Database\Seeders;

use App\Enums\ProductType as ProductTypeEnum;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Subcategory;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FinalCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $supplier = Supplier::query()->updateOrCreate(
            ['email' => 'catalog@meacash.com'],
            [
                'name' => 'MeaCash Final Catalog Supply',
                'contact_name' => 'Catalog Team',
                'phone' => '70000111',
                'notes' => 'Final seed supplier for real demo catalog products.',
                'is_active' => true,
            ]
        );

        $packageOnlyType = ProductType::query()->updateOrCreate(
            ['key' => 'final-package-only'],
            [
                'name' => 'Package Only',
                'description' => 'Fixed digital product with optional instant/manual fulfillment.',
                'is_active' => true,
                'schema' => [
                    'modes' => ['package'],
                    'fields' => [],
                ],
            ]
        );

        foreach ($this->catalog() as $categoryIndex => $categoryData) {
            $category = Category::query()->updateOrCreate(
                ['slug' => Str::slug($categoryData['name_en'])],
                [
                    'name_en' => $categoryData['name_en'],
                    'name_ar' => $categoryData['name_ar'],
                    'icon' => $categoryData['icon'],
                    'image' => $categoryData['image'],
                    'is_active' => true,
                    'sort_order' => ($categoryIndex + 1) * 10,
                    'seo_title' => $categoryData['name_en'].' | MeaCash',
                    'seo_description' => 'Buy '.$categoryData['name_en'].' digital products with fast delivery on MeaCash.',
                ]
            );

            foreach ($categoryData['subcategories'] as $subcategoryIndex => $brand) {
                $subcategory = Subcategory::query()->updateOrCreate(
                    ['slug' => Str::slug($brand['name'])],
                    [
                        'category_id' => $category->id,
                        'product_type_id' => $packageOnlyType->id,
                        'name_en' => $brand['name'],
                        'name_ar' => $brand['name_ar'] ?? $brand['name'],
                        'description_en' => 'Real '.$brand['name'].' digital products and top-up packages with fast MeaCash processing.',
                        'description_ar' => 'منتجات وباقات رقمية حقيقية لـ '.$brand['name'].' مع معالجة سريعة من MeaCash.',
                        'image' => $brand['image'],
                        'is_active' => true,
                        'is_featured' => $subcategoryIndex < 2,
                        'sort_order' => ($subcategoryIndex + 1) * 10,
                        'seo_title' => 'Buy '.$brand['name'].' | MeaCash',
                        'seo_description' => 'Secure '.$brand['name'].' digital products, gift cards, and subscriptions.',
                        'seo_image' => $brand['image'],
                    ]
                );

                foreach ($brand['products'] as $productIndex => $productData) {
                    $price = $productData['price'];
                    $cost = round($price * 0.84, 2);
                    $delivery = $productData['delivery'] ?? 'instant';

                    Product::query()->updateOrCreate(
                        ['slug' => Str::slug($brand['name'].' '.$productData['name'])],
                        [
                            'subcategory_id' => $subcategory->id,
                            'supplier_id' => $supplier->id,
                            'product_type_id' => $packageOnlyType->id,
                            'name_en' => $brand['name'].' - '.$productData['name'],
                            'name_ar' => ($brand['name_ar'] ?? $brand['name']).' - '.$productData['name_ar'],
                            'description_en' => $productData['description'] ?? 'Official '.$brand['name'].' digital product delivered through MeaCash.',
                            'description_ar' => $productData['description_ar'] ?? 'منتج رقمي رسمي لـ '.$brand['name'].' يتم تسليمه عبر MeaCash.',
                            'product_type' => ProductTypeEnum::FixedPackage->value,
                            'delivery_type' => $delivery,
                            'delivery_time_minutes' => $delivery === 'manual' ? 30 : 5,
                            'cost_price' => $cost,
                            'selling_price' => $price,
                            'price_per_unit' => null,
                            'min_quantity' => 1,
                            'max_quantity' => null,
                            'image' => $brand['image'],
                            'is_active' => true,
                            'is_featured' => $productIndex === 0,
                            'stock_alert_threshold' => 5,
                            'seo_title' => 'Buy '.$brand['name'].' '.$productData['name'].' | MeaCash',
                            'seo_description' => 'Fast and secure '.$brand['name'].' '.$productData['name'].' from MeaCash.',
                            'seo_image' => $brand['image'],
                            'sort_order' => $productIndex + 1,
                        ]
                    );
                }
            }
        }

        Cache::forget('storefront:homepage-section-ids');
        Cache::forget('storefront:homepage-sections');
    }

    private function logo(string $domain): string
    {
        return "https://www.google.com/s2/favicons?domain={$domain}&sz=128";
    }

    private function products(array $names, string $brand, float $basePrice = 5.0): array
    {
        return collect($names)->map(function (string $name, int $index) use ($brand, $basePrice): array {
            $price = round($basePrice + (($index + 1) * 3.75), 2);

            return [
                'name' => $name,
                'name_ar' => $name,
                'price' => $price,
                'description' => "Official {$brand} {$name} product with secure MeaCash processing.",
                'description_ar' => "منتج {$brand} {$name} رسمي مع معالجة آمنة عبر MeaCash.",
                'delivery' => 'instant',
            ];
        })->all();
    }

    private function catalog(): array
    {
        return [
            [
                'name_en' => 'Gaming Vault',
                'name_ar' => 'خزنة الألعاب',
                'icon' => 'sports_esports',
                'image' => $this->logo('riotgames.com'),
                'subcategories' => [
                    ['name' => 'PUBG Mobile', 'image' => $this->logo('pubgmobile.com'), 'products' => $this->products(['60 UC', '325 UC', '660 UC', '1800 UC'], 'PUBG Mobile', 2)],
                    ['name' => 'Free Fire', 'image' => $this->logo('garena.com'), 'products' => $this->products(['110 Diamonds', '341 Diamonds', '572 Diamonds', '1166 Diamonds'], 'Free Fire', 1.8)],
                    ['name' => 'Roblox', 'image' => $this->logo('roblox.com'), 'products' => $this->products(['400 Robux', '800 Robux', '1700 Robux', '4500 Robux'], 'Roblox', 4)],
                    ['name' => 'Valorant', 'image' => $this->logo('playvalorant.com'), 'products' => $this->products(['475 VP', '1000 VP', '2050 VP', '3650 VP'], 'Valorant', 4.5)],
                ],
            ],
            [
                'name_en' => 'Console Cards',
                'name_ar' => 'بطاقات الكونسول',
                'icon' => 'videogame_asset',
                'image' => $this->logo('playstation.com'),
                'subcategories' => [
                    ['name' => 'PlayStation Store', 'image' => $this->logo('playstation.com'), 'products' => $this->products(['10 USD Card', '25 USD Card', '50 USD Card', '100 USD Card'], 'PlayStation Store', 7)],
                    ['name' => 'Xbox Gift Card', 'image' => $this->logo('xbox.com'), 'products' => $this->products(['10 USD Card', '25 USD Card', '50 USD Card', '100 USD Card'], 'Xbox Gift Card', 7)],
                    ['name' => 'Nintendo eShop', 'image' => $this->logo('nintendo.com'), 'products' => $this->products(['10 USD Card', '20 USD Card', '35 USD Card', '50 USD Card'], 'Nintendo eShop', 7)],
                    ['name' => 'Steam Wallet', 'image' => $this->logo('steampowered.com'), 'products' => $this->products(['5 USD Card', '10 USD Card', '20 USD Card', '50 USD Card'], 'Steam Wallet', 3.5)],
                ],
            ],
            [
                'name_en' => 'Streaming',
                'name_ar' => 'البث والترفيه',
                'icon' => 'movie',
                'image' => $this->logo('netflix.com'),
                'subcategories' => [
                    ['name' => 'Netflix', 'image' => $this->logo('netflix.com'), 'products' => $this->products(['1 Month Standard', '1 Month Premium', '3 Months Standard', '3 Months Premium'], 'Netflix', 4)],
                    ['name' => 'Spotify Premium', 'image' => $this->logo('spotify.com'), 'products' => $this->products(['1 Month Individual', '3 Months Individual', '6 Months Individual', '12 Months Individual'], 'Spotify Premium', 2.5)],
                    ['name' => 'YouTube Premium', 'image' => $this->logo('youtube.com'), 'products' => $this->products(['1 Month', '3 Months', '6 Months', '12 Months'], 'YouTube Premium', 3.5)],
                    ['name' => 'Apple TV', 'image' => $this->logo('apple.com'), 'products' => $this->products(['1 Month', '3 Months', '6 Months', '12 Months'], 'Apple TV', 3)],
                ],
            ],
            [
                'name_en' => 'Gift Cards',
                'name_ar' => 'بطاقات الهدايا',
                'icon' => 'card_giftcard',
                'image' => $this->logo('amazon.com'),
                'subcategories' => [
                    ['name' => 'Amazon Global', 'image' => $this->logo('amazon.com'), 'products' => $this->products(['10 USD Card', '25 USD Card', '50 USD Card', '100 USD Card'], 'Amazon Global', 7)],
                    ['name' => 'Google Play', 'image' => $this->logo('google.com'), 'products' => $this->products(['10 USD Card', '25 USD Card', '50 USD Card', '100 USD Card'], 'Google Play', 7)],
                    ['name' => 'Apple Gift Card', 'image' => $this->logo('apple.com'), 'products' => $this->products(['10 USD Card', '25 USD Card', '50 USD Card', '100 USD Card'], 'Apple Gift Card', 7)],
                    ['name' => 'Razer Gold', 'image' => $this->logo('razer.com'), 'products' => $this->products(['10 USD PIN', '25 USD PIN', '50 USD PIN', '100 USD PIN'], 'Razer Gold', 7)],
                ],
            ],
            [
                'name_en' => 'Social Apps',
                'name_ar' => 'تطبيقات التواصل',
                'icon' => 'forum',
                'image' => $this->logo('tiktok.com'),
                'subcategories' => [
                    ['name' => 'TikTok Coins', 'image' => $this->logo('tiktok.com'), 'products' => $this->products(['70 Coins', '350 Coins', '700 Coins', '1400 Coins'], 'TikTok Coins', 1.5)],
                    ['name' => 'Discord Nitro', 'image' => $this->logo('discord.com'), 'products' => $this->products(['Basic 1 Month', 'Nitro 1 Month', 'Nitro 3 Months', 'Nitro 12 Months'], 'Discord Nitro', 2.5)],
                    ['name' => 'Twitch', 'image' => $this->logo('twitch.tv'), 'products' => $this->products(['Bits 100', 'Bits 500', 'Sub 1 Month', 'Sub 3 Months'], 'Twitch', 2)],
                    ['name' => 'BIGO Live', 'image' => $this->logo('bigo.tv'), 'products' => $this->products(['100 Beans', '500 Beans', '1000 Beans', '5000 Beans'], 'BIGO Live', 2)],
                ],
            ],
            [
                'name_en' => 'Software',
                'name_ar' => 'البرامج',
                'icon' => 'desktop_windows',
                'image' => $this->logo('microsoft.com'),
                'subcategories' => [
                    ['name' => 'Microsoft Windows', 'image' => $this->logo('microsoft.com'), 'products' => $this->products(['Windows 11 Home', 'Windows 11 Pro', 'Windows 10 Pro', 'Windows Server Key'], 'Microsoft Windows', 8)],
                    ['name' => 'Microsoft Office', 'image' => $this->logo('microsoft.com'), 'products' => $this->products(['Office 2019 Pro', 'Office 2021 Pro', 'Microsoft 365 1 Month', 'Microsoft 365 1 Year'], 'Microsoft Office', 7)],
                    ['name' => 'Adobe Creative Cloud', 'image' => $this->logo('adobe.com'), 'products' => $this->products(['Photoshop 1 Month', 'Illustrator 1 Month', 'Premiere Pro 1 Month', 'All Apps 1 Month'], 'Adobe Creative Cloud', 6)],
                    ['name' => 'Canva Pro', 'image' => $this->logo('canva.com'), 'products' => $this->products(['1 Month Invite', '3 Months Invite', '6 Months Invite', '1 Year Invite'], 'Canva Pro', 2)],
                ],
            ],
            [
                'name_en' => 'Security',
                'name_ar' => 'الحماية',
                'icon' => 'shield',
                'image' => $this->logo('nordvpn.com'),
                'subcategories' => [
                    ['name' => 'Kaspersky', 'image' => $this->logo('kaspersky.com'), 'products' => $this->products(['1 Device 1 Year', '3 Devices 1 Year', '5 Devices 1 Year', '10 Devices 1 Year'], 'Kaspersky', 6)],
                    ['name' => 'Bitdefender', 'image' => $this->logo('bitdefender.com'), 'products' => $this->products(['Antivirus Plus', 'Internet Security', 'Total Security', 'Family Pack'], 'Bitdefender', 6)],
                    ['name' => 'NordVPN', 'image' => $this->logo('nordvpn.com'), 'products' => $this->products(['1 Month', '6 Months', '1 Year', '2 Years'], 'NordVPN', 4)],
                    ['name' => 'McAfee', 'image' => $this->logo('mcafee.com'), 'products' => $this->products(['Single Device', 'Multi Device', 'Family Plan', 'Total Protection'], 'McAfee', 5)],
                ],
            ],
            [
                'name_en' => 'Education',
                'name_ar' => 'التعليم',
                'icon' => 'school',
                'image' => $this->logo('coursera.org'),
                'subcategories' => [
                    ['name' => 'Udemy', 'image' => $this->logo('udemy.com'), 'products' => $this->products(['10 USD Voucher', '25 USD Voucher', '50 USD Voucher', '100 USD Voucher'], 'Udemy', 7)],
                    ['name' => 'Coursera', 'image' => $this->logo('coursera.org'), 'products' => $this->products(['1 Month Plus', '3 Months Plus', '6 Months Plus', '1 Year Plus'], 'Coursera', 10)],
                    ['name' => 'Skillshare', 'image' => $this->logo('skillshare.com'), 'products' => $this->products(['1 Month', '3 Months', '6 Months', '1 Year'], 'Skillshare', 3)],
                    ['name' => 'Duolingo', 'image' => $this->logo('duolingo.com'), 'products' => $this->products(['Super 1 Month', 'Super 3 Months', 'Super 6 Months', 'Super 1 Year'], 'Duolingo', 3)],
                ],
            ],
            [
                'name_en' => 'Work Tools',
                'name_ar' => 'أدوات العمل',
                'icon' => 'work',
                'image' => $this->logo('notion.so'),
                'subcategories' => [
                    ['name' => 'OpenAI', 'image' => $this->logo('openai.com'), 'products' => $this->products(['ChatGPT Plus 1 Month', 'API Credit 10 USD', 'API Credit 25 USD', 'API Credit 50 USD'], 'OpenAI', 7)],
                    ['name' => 'Zoom', 'image' => $this->logo('zoom.us'), 'products' => $this->products(['Pro 1 Month', 'Pro 3 Months', 'Business 1 Month', 'Webinar Add-on'], 'Zoom', 5)],
                    ['name' => 'Notion', 'image' => $this->logo('notion.so'), 'products' => $this->products(['Plus 1 Month', 'Plus 3 Months', 'AI Add-on', 'Business 1 Month'], 'Notion', 4)],
                    ['name' => 'Dropbox', 'image' => $this->logo('dropbox.com'), 'products' => $this->products(['Plus 1 Month', 'Plus 3 Months', 'Professional 1 Month', 'Professional 1 Year'], 'Dropbox', 6)],
                ],
            ],
        ];
    }
}
