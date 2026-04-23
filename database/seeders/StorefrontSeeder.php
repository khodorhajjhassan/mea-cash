<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StorefrontSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure a Supplier exists
        $supplier = Supplier::firstOrCreate(
            ['email' => 'tech@meacash.com'],
            [
                'name' => 'MeaCash Global Supply',
                'contact_name' => 'Techno King',
                'is_active' => true,
            ]
        );

        // 2. Clear existing Storefront Data to ensure exact counts
        Product::truncate();
        Subcategory::truncate();
        Category::truncate();

        // 3. Define the 6 Categories
        $categories = [
            [
                'name_en' => 'Gaming Vault', 
                'name_ar' => 'فضاء الألعاب', 
                'icon' => 'sports_esports', 
                'subcategories' => ['PUBG Mobile', 'Free Fire', 'Roblox', 'League of Legends']
            ],
            [
                'name_en' => 'Gift Cards', 
                'name_ar' => 'بطاقات الهدايا', 
                'icon' => 'card_giftcard', 
                'subcategories' => ['iTunes Store', 'Google Play', 'Amazon Global']
            ],
            [
                'name_en' => 'Consoles', 
                'name_ar' => 'أجهزة الكونسول', 
                'icon' => 'videogame_asset', 
                'subcategories' => ['PlayStation', 'Xbox Live', 'Nintendo eShop']
            ],
            [
                'name_en' => 'Streaming', 
                'name_ar' => 'بث الترفيه', 
                'icon' => 'movie', 
                'subcategories' => ['Spotify Premium', 'Netflix Global']
            ],
            [
                'name_en' => 'Software', 
                'name_ar' => 'البرامج الرقمية', 
                'icon' => 'desktop_windows', 
                'subcategories' => ['Windows 11', 'Adobe Creative']
            ],
            [
                'name_en' => 'Social Apps', 
                'name_ar' => 'تطبيقات التواصل', 
                'icon' => 'forum', 
                'subcategories' => ['TikTok Coins', 'Discord Nitro']
            ],
        ];

        $subcatCount = 0;
        foreach ($categories as $catData) {
            $category = Category::create([
                'name_en' => $catData['name_en'],
                'name_ar' => $catData['name_ar'],
                'slug' => Str::slug($catData['name_en']),
                'icon' => $catData['icon'],
                'is_active' => true,
                'sort_order' => 0,
            ]);

            // Create Subcategories (Limit to 8 total across all categories as requested by USER)
            foreach ($catData['subcategories'] as $subName) {
                if ($subcatCount >= 8) break;

                $subcategory = Subcategory::create([
                    'category_id' => $category->id,
                    'name_en' => $subName,
                    'name_ar' => $subName, // Use same for brevity or translate
                    'slug' => Str::slug($subName),
                    'is_active' => true,
                ]);

                $subcatCount++;

                // 4. Add 2 to 6 Products for each Subcategory
                $productCount = rand(2, 6);
                for ($i = 1; $i <= $productCount; $i++) {
                    $price = rand(5, 100);
                    Product::create([
                        'subcategory_id' => $subcategory->id,
                        'supplier_id' => $supplier->id,
                        'name_en' => "{$subName} - Package {$i}",
                        'name_ar' => "{$subName} - باقة {$i}",
                        'description_en' => "Premium digital asset protocol for {$subName}. Safe and fast delivery guaranteed.",
                        'description_ar' => "بروتوكول أصول رقمية متميزة لـ {$subName}. تسليم آمن وسريع مضمون.",
                        'slug' => Str::slug("{$subName} Package {$i}"),                        'delivery_type' => 'instant',
                        'cost_price' => $price * 0.8,
                        'selling_price' => $price,
                        'is_active' => true,
                        'is_featured' => $i === 1,
                        'seo_title' => "Buy {$subName} Package {$i} | MeaCash",
                    ]);
                }
            }
        }
    }
}

