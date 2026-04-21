<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedbacks', function (Blueprint $table): void {
            $table->dropUnique(['user_id', 'order_id']);
            $table->enum('type', ['feedback', 'report'])->default('feedback')->after('order_id')->index();
            $table->string('issue_type')->nullable()->after('comment');
            $table->enum('status', ['open', 'reviewing', 'resolved', 'refunded'])
                ->default('open')
                ->after('issue_type')
                ->index();
            $table->text('admin_response')->nullable()->after('status');
            $table->timestamp('resolved_at')->nullable()->after('admin_response');
            $table->unique(['user_id', 'order_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::table('feedbacks', function (Blueprint $table): void {
            $table->dropUnique(['user_id', 'order_id', 'type']);
            $table->dropColumn(['type', 'issue_type', 'status', 'admin_response', 'resolved_at']);
            $table->unique(['user_id', 'order_id']);
        });
    }
};
