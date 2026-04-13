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
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\TopupRequest;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create();

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@meacash.com'],
            [
                'name' => 'MeaCash Admin',
                'phone' => '70000000',
                'password' => Hash::make('password'),
                'preferred_language' => 'en',
                'is_active' => true,
                'is_admin' => true,
            ]
        );

        Wallet::query()->firstOrCreate(
            ['user_id' => $admin->id],
            ['balance' => 0, 'currency' => 'USD']
        );

        $suppliers = collect([
            ['name' => 'Global Pins Hub', 'contact_name' => 'Nora Ali', 'email' => 'sales@globalpins.example', 'phone' => '71110001', 'notes' => 'Primary code supplier', 'is_active' => true],
            ['name' => 'TopUp Express', 'contact_name' => 'Rami S', 'email' => 'ops@topupexpress.example', 'phone' => '71110002', 'notes' => 'Fast delivery for gaming', 'is_active' => true],
            ['name' => 'Digital Wallet Source', 'contact_name' => 'Mina K', 'email' => 'support@dwsource.example', 'phone' => '71110003', 'notes' => 'Flexible payment products', 'is_active' => true],
            ['name' => 'GiftCard Depot', 'contact_name' => 'Rana F', 'email' => 'team@giftcarddepot.example', 'phone' => '71110004', 'notes' => null, 'is_active' => true],
        ])->map(fn (array $data) => Supplier::query()->updateOrCreate(['name' => $data['name']], $data));

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
            'Payments' => [
                'USDT (TRC20)' => ['10 USD', '25 USD', '50 USD', '100 USD'],
                'Perfect Money' => ['10 USD', '25 USD', '50 USD', '100 USD'],
                'Payeer' => ['10 USD', '20 USD', '50 USD', '100 USD'],
            ],
        ];

        $allProducts = collect();

        foreach ($catalogMap as $categoryName => $subcategoriesData) {
            $category = Category::query()->updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                [
                    'name_en' => $categoryName,
                    'name_ar' => $categoryName,
                    'icon' => 'grid',
                    'image' => null,
                    'is_active' => true,
                    'sort_order' => rand(1, 50),
                    'seo_title' => $categoryName.' Digital Topups',
                    'seo_description' => 'Fast and secure '.$categoryName.' topups and gift cards.',
                ]
            );

            foreach ($subcategoriesData as $subcategoryName => $packages) {
                $subcategory = Subcategory::query()->updateOrCreate(
                    ['slug' => Str::slug($subcategoryName)],
                    [
                        'category_id' => $category->id,
                        'name_en' => $subcategoryName,
                        'name_ar' => $subcategoryName,
                        'image' => null,
                        'is_active' => true,
                        'sort_order' => rand(1, 100),
                        'seo_title' => $subcategoryName.' Topup',
                        'seo_description' => 'Buy '.$subcategoryName.' packages instantly.',
                    ]
                );

                $supplierId = $suppliers->random()->id;
                $isCustom = in_array($subcategoryName, ['BIGO Live', 'TikTok Coins'], true);

                $product = Product::query()->updateOrCreate(
                    ['slug' => Str::slug($subcategoryName.' package')],
                    [
                        'subcategory_id' => $subcategory->id,
                        'supplier_id' => $supplierId,
                        'name_en' => $subcategoryName.' Recharge',
                        'name_ar' => $subcategoryName.' Recharge',
                        'description_en' => 'Instant delivery for '.$subcategoryName,
                        'description_ar' => 'Instant delivery for '.$subcategoryName,
                        'product_type' => $isCustom ? 'custom_quantity' : 'fixed_package',
                        'delivery_type' => 'instant',
                        'delivery_time_minutes' => 5,
                        'cost_price' => 1,
                        'selling_price' => 1.2,
                        'price_per_unit' => $isCustom ? 0.01 : null,
                        'min_quantity' => 1,
                        'max_quantity' => $isCustom ? 1000000 : null,
                        'image' => null,
                        'is_active' => true,
                        'is_featured' => (bool) rand(0, 1),
                        'stock_alert_threshold' => 5,
                        'seo_title' => $subcategoryName.' Digital Topup',
                        'seo_description' => 'Best rates for '.$subcategoryName,
                        'seo_keywords' => strtolower($subcategoryName).', topup, card',
                        'sort_order' => rand(1, 100),
                    ]
                );

                $allProducts->push($product);

                ProductFormField::query()->updateOrCreate(
                    ['product_id' => $product->id, 'field_key' => 'account_id'],
                    [
                        'label_en' => 'Account ID',
                        'label_ar' => 'Account ID',
                        'field_type' => 'text',
                        'placeholder_en' => 'Enter account ID',
                        'placeholder_ar' => 'Enter account ID',
                        'is_required' => true,
                        'sort_order' => 1,
                        'validation_rules' => ['required', 'string', 'max:120'],
                    ]
                );

                if ($subcategoryName === 'TikTok Coins') {
                    ProductFormField::query()->updateOrCreate(
                        ['product_id' => $product->id, 'field_key' => 'email'],
                        [
                            'label_en' => 'Login Email',
                            'label_ar' => 'Login Email',
                            'field_type' => 'email',
                            'placeholder_en' => 'Email',
                            'placeholder_ar' => 'Email',
                            'is_required' => true,
                            'sort_order' => 2,
                            'validation_rules' => ['required', 'email'],
                        ]
                    );
                }

                foreach ($packages as $index => $packageName) {
                    $amount = (float) preg_replace('/[^0-9.]/', '', $packageName) ?: ($index + 1) * 10;
                    $costPrice = max(0.5, $amount * 0.008);
                    $sellPrice = round($costPrice * 1.15, 2);

                    $package = ProductPackage::query()->create([
                        'product_id' => $product->id,
                        'name_en' => $packageName,
                        'name_ar' => $packageName,
                        'amount' => $amount,
                        'cost_price' => $costPrice,
                        'selling_price' => $sellPrice,
                        'image' => null,
                        'badge_text' => $index === 0 ? 'Fast' : null,
                        'is_available' => true,
                        'sort_order' => $index + 1,
                    ]);

                    for ($i = 0; $i < 15; $i++) {
                        ProductCode::query()->create([
                            'product_id' => $product->id,
                            'package_id' => $package->id,
                            'code' => strtoupper(Str::random(5)).'-'.strtoupper(Str::random(5)).'-'.strtoupper(Str::random(5)),
                            'notes' => null,
                            'status' => 'available',
                        ]);
                    }
                }
            }
        }

        PaymentMethod::query()->updateOrCreate(
            ['method' => 'omt'],
            [
                'display_name_en' => 'OMT',
                'display_name_ar' => 'OMT',
                'account_identifier' => '70-123456',
                'instructions_en' => 'Send payment then upload receipt.',
                'instructions_ar' => 'Send payment then upload receipt.',
                'is_active' => true,
            ]
        );

        PaymentMethod::query()->updateOrCreate(
            ['method' => 'wish'],
            [
                'display_name_en' => 'Wish Money',
                'display_name_ar' => 'Wish Money',
                'account_identifier' => '70-654321',
                'instructions_en' => 'Transfer then submit screenshot.',
                'instructions_ar' => 'Transfer then submit screenshot.',
                'is_active' => true,
            ]
        );

        PaymentMethod::query()->updateOrCreate(
            ['method' => 'usdt'],
            [
                'display_name_en' => 'USDT TRC20',
                'display_name_ar' => 'USDT TRC20',
                'account_identifier' => 'TQ7QhFakeAddressDemoSeedOnly',
                'instructions_en' => 'Send exact amount and submit txid.',
                'instructions_ar' => 'Send exact amount and submit txid.',
                'is_active' => true,
            ]
        );

        $users = collect();

        for ($i = 1; $i <= 120; $i++) {
            $user = User::query()->create([
                'name' => $faker->name(),
                'email' => 'user'.$i.'@meacash.dev',
                'phone' => '79'.str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                'password' => Hash::make('password'),
                'preferred_language' => $faker->randomElement(['en', 'ar']),
                'is_active' => true,
                'is_admin' => false,
            ]);

            $wallet = Wallet::query()->create([
                'user_id' => $user->id,
                'balance' => rand(20, 700),
                'currency' => 'USD',
            ]);

            for ($t = 0; $t < rand(2, 8); $t++) {
                $amount = rand(5, 120);
                $before = (float) $wallet->balance;
                $after = $before + $amount;
                $wallet->update(['balance' => $after]);

                WalletTransaction::query()->create([
                    'wallet_id' => $wallet->id,
                    'type' => 'topup',
                    'amount' => $amount,
                    'balance_before' => $before,
                    'balance_after' => $after,
                    'description_en' => 'Seed topup transaction',
                    'description_ar' => 'Seed topup transaction',
                    'created_at' => now()->subDays(rand(1, 60)),
                ]);
            }

            $users->push($user);
        }

        for ($t = 0; $t < 140; $t++) {
            $user = $users->random();
            TopupRequest::query()->create([
                'user_id' => $user->id,
                'payment_method' => collect(['omt', 'wish', 'usdt'])->random(),
                'amount_requested' => rand(10, 250),
                'receipt_image_path' => 'receipts/demo-'.$t.'.webp',
                'status' => collect(['pending', 'approved', 'rejected'])->random(),
                'admin_note' => rand(0, 1) ? 'Checked by finance team' : null,
                'processed_by' => $admin->id,
                'processed_at' => now()->subDays(rand(0, 45)),
            ]);
        }

        $orderCounter = 1;

        foreach ($users as $user) {
            for ($o = 0; $o < rand(1, 7); $o++) {
                $product = $allProducts->random();
                $package = $product->packages()->inRandomOrder()->first();

                $unitPrice = (float) ($package?->selling_price ?? $product->selling_price);
                $costPrice = (float) ($package?->cost_price ?? $product->cost_price);
                $quantity = rand(1, 3);
                $total = round($unitPrice * $quantity, 2);

                $order = Order::query()->create([
                    'order_number' => 'MC-2026-'.str_pad((string) $orderCounter++, 6, '0', STR_PAD_LEFT),
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'package_id' => $package?->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $total,
                    'cost_price' => round($costPrice * $quantity, 2),
                    'profit' => round(($unitPrice - $costPrice) * $quantity, 2),
                    'status' => collect(['pending', 'processing', 'completed', 'failed', 'refunded'])->random(),
                    'delivery_type' => $product->delivery_type,
                    'fulfillment_data' => [
                        'account_id' => 'ACC'.rand(100000, 999999),
                    ],
                    'delivery_notes' => null,
                    'fulfilled_at' => now()->subDays(rand(0, 40)),
                    'confirmed_at' => rand(0, 1) ? now()->subDays(rand(0, 35)) : null,
                    'created_at' => now()->subDays(rand(0, 60)),
                    'updated_at' => now()->subDays(rand(0, 30)),
                ]);

                $availableCode = ProductCode::query()
                    ->where('product_id', $product->id)
                    ->where('status', 'available')
                    ->inRandomOrder()
                    ->first();

                if ($availableCode) {
                    $availableCode->update([
                        'status' => 'sold',
                        'order_id' => $order->id,
                        'used_at' => now(),
                    ]);
                }

                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'code_id' => $availableCode?->id,
                    'delivered_value' => $availableCode?->code ?? 'Manual fulfillment note',
                    'type' => $availableCode ? 'code' : 'manual_note',
                    'revealed_at' => now()->subDays(rand(0, 20)),
                ]);

                if ($order->status === 'completed' && rand(0, 100) < 55) {
                    Feedback::query()->create([
                        'user_id' => $user->id,
                        'order_id' => $order->id,
                        'rating' => rand(3, 5),
                        'comment' => $faker->sentence(),
                    ]);
                }
            }
        }

        for ($c = 0; $c < 90; $c++) {
            ContactMessage::query()->create([
                'name' => $faker->name(),
                'email' => $faker->safeEmail(),
                'subject' => $faker->sentence(3),
                'message' => $faker->paragraph(),
                'responded_at' => rand(0, 1) ? now()->subDays(rand(0, 20)) : null,
            ]);
        }

        foreach ([
            ['key' => 'site_name', 'value' => 'MeaCash', 'group' => 'general'],
            ['key' => 'maintenance_mode', 'value' => 'off', 'group' => 'general'],
            ['key' => 'support_email', 'value' => 'support@meacash.com', 'group' => 'general'],
            ['key' => 'seo_default_title', 'value' => 'MeaCash | Digital Topups', 'group' => 'seo'],
            ['key' => 'seo_default_description', 'value' => 'Fast digital topup and gift cards in Lebanon.', 'group' => 'seo'],
            ['key' => 'payment_receipt_visibility', 'value' => 'private', 'group' => 'payment'],
        ] as $setting) {
            AdminSetting::query()->updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
