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
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->nullable()->constrained('product_packages')->nullOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->decimal('profit', 12, 2)->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded'])->default('pending')->index();
            $table->enum('delivery_type', ['instant', 'timed', 'manual'])->index();
            $table->json('fulfillment_data')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'created_at']);
            $table->index(['product_id', 'status']);
        });

        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('code_id')->nullable()->constrained('product_codes')->nullOnDelete();
            $table->text('delivered_value')->nullable();
            $table->enum('type', ['code', 'account_credentials', 'manual_note']);
            $table->timestamp('revealed_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'type']);
        });

        Schema::table('product_codes', function (Blueprint $table): void {
            $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_codes', function (Blueprint $table): void {
            $table->dropForeign(['order_id']);
        });

        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
