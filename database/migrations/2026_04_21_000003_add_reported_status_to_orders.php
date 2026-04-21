<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','processing','completed','reported','failed','refunded','canceled') NOT NULL DEFAULT 'pending'");
        } elseif (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check');
            DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status::text = ANY (ARRAY['pending'::character varying, 'processing'::character varying, 'completed'::character varying, 'reported'::character varying, 'failed'::character varying, 'refunded'::character varying, 'canceled'::character varying]::text[]))");
        }

        $reportedOrderIds = DB::table('feedbacks')
            ->where('type', 'report')
            ->whereIn('status', ['open', 'reviewing'])
            ->pluck('order_id')
            ->filter()
            ->all();

        if ($reportedOrderIds !== []) {
            DB::table('orders')
                ->whereIn('id', $reportedOrderIds)
                ->where('status', 'completed')
                ->update(['status' => 'reported']);
        }
    }

    public function down(): void
    {
        DB::table('orders')
            ->where('status', 'reported')
            ->update(['status' => 'completed']);

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','processing','completed','failed','refunded') NOT NULL DEFAULT 'pending'");
        } elseif (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check');
            DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status::text = ANY (ARRAY['pending'::character varying, 'processing'::character varying, 'completed'::character varying, 'failed'::character varying, 'refunded'::character varying]::text[]))");
        }
    }
};
