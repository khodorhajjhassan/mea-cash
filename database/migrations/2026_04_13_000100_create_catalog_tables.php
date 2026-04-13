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
        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
        });

        Schema::create('subcategories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();

            $table->index(['category_id', 'is_active']);
        });

        Schema::create('suppliers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('subcategory_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('slug')->unique();
            $table->enum('product_type', ['fixed_package', 'custom_quantity', 'account_topup', 'manual_service'])->index();
            $table->enum('delivery_type', ['instant', 'timed', 'manual'])->index();
            $table->unsignedInteger('delivery_time_minutes')->nullable();
            $table->decimal('cost_price', 10, 4)->default(0);
            $table->decimal('selling_price', 10, 4)->default(0);
            $table->decimal('price_per_unit', 10, 4)->nullable();
            $table->unsignedInteger('min_quantity')->default(1);
            $table->unsignedInteger('max_quantity')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('stock_alert_threshold')->default(5);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();

            $table->index(['subcategory_id', 'is_active']);
            $table->index(['product_type', 'is_active']);
            $table->index(['delivery_type', 'is_active']);
        });

        Schema::create('product_packages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name_ar');
            $table->string('name_en');
            $table->decimal('amount', 14, 4);
            $table->decimal('cost_price', 10, 4)->default(0);
            $table->decimal('selling_price', 10, 4)->default(0);
            $table->string('image')->nullable();
            $table->string('badge_text')->nullable();
            $table->boolean('is_available')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();

            $table->index(['product_id', 'is_available']);
        });

        Schema::create('product_form_fields', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('field_key');
            $table->string('label_ar');
            $table->string('label_en');
            $table->enum('field_type', ['text', 'email', 'password', 'number', 'select']);
            $table->string('placeholder_ar')->nullable();
            $table->string('placeholder_en')->nullable();
            $table->boolean('is_required')->default(true);
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->json('validation_rules')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'field_key']);
        });

        Schema::create('product_codes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->nullable()->constrained('product_packages')->nullOnDelete();
            $table->text('code');
            $table->text('notes')->nullable();
            $table->enum('status', ['available', 'reserved', 'sold', 'failed'])->default('available')->index();
            $table->foreignId('order_id')->nullable()->index();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'package_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_codes');
        Schema::dropIfExists('product_form_fields');
        Schema::dropIfExists('product_packages');
        Schema::dropIfExists('products');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('subcategories');
        Schema::dropIfExists('categories');
    }
};
