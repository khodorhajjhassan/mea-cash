<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Alter column type to text to support encrypted strings (base64)
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('orders', function (Blueprint $table) {
                $table->text('fulfillment_data')->nullable()->change();
            });
        }

        // 2. One-way migration to encrypt plain-text fulfillment_data
        DB::table('orders')->whereNotNull('fulfillment_data')->orderBy('id')->chunk(100, function ($orders) {
            foreach ($orders as $order) {
                $rawData = $order->fulfillment_data;
                
                if (empty($rawData)) continue;

                try {
                    // Check if already encrypted
                    decrypt($rawData);
                    continue;
                } catch (DecryptException $e) {
                    // It is plain text JSON, encrypt it
                    DB::table('orders')
                        ->where('id', $order->id)
                        ->update([
                            'fulfillment_data' => encrypt($rawData)
                        ]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down() method as encryption is one-way for security.
    }
};
