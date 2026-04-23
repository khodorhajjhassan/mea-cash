<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_form_fields')) {
            Schema::drop('product_form_fields');
        }

        $hasProductType = Schema::hasColumn('products', 'product_type');
        $hasProductTypeId = Schema::hasColumn('products', 'product_type_id');

        Schema::table('products', function (Blueprint $table) use ($hasProductType, $hasProductTypeId): void {
            if ($hasProductType) {
                $table->dropIndex('products_product_type_index');
                $table->dropIndex('products_product_type_is_active_index');
            }

            if ($hasProductTypeId) {
                $table->dropConstrainedForeignId('product_type_id');
            }

            if ($hasProductType) {
                $table->dropColumn('product_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            if (! Schema::hasColumn('products', 'product_type')) {
                $table->enum('product_type', ['fixed_package', 'custom_quantity', 'account_topup', 'manual_service'])
                    ->default('fixed_package')
                    ->index();
            }

            if (! Schema::hasColumn('products', 'product_type_id')) {
                $table->foreignId('product_type_id')
                    ->nullable()
                    ->after('supplier_id')
                    ->constrained('product_types')
                    ->nullOnDelete();
            }
        });

        if (! Schema::hasTable('product_form_fields')) {
            Schema::create('product_form_fields', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('field_key');
                $table->string('label_en');
                $table->string('label_ar')->nullable();
                $table->string('field_type')->default('text');
                $table->string('placeholder_en')->nullable();
                $table->string('placeholder_ar')->nullable();
                $table->boolean('is_required')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->json('validation_rules')->nullable();
                $table->json('ui_meta')->nullable();
                $table->timestamps();
            });
        }
    }
};
