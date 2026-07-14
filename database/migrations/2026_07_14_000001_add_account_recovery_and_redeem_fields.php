nd <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('remember_token');
            }

            if (! Schema::hasColumn('users', 'password_reset_code')) {
                $table->string('password_reset_code', 6)->nullable()->after('email_verification_expires_at');
            }

            if (! Schema::hasColumn('users', 'password_reset_expires_at')) {
                $table->timestamp('password_reset_expires_at')->nullable()->after('password_reset_code');
            }
        });

        Schema::table('subcategories', function (Blueprint $table): void {
            if (! Schema::hasColumn('subcategories', 'how_to_redeem_en')) {
                $table->text('how_to_redeem_en')->nullable()->after('description_en');
            }

            if (! Schema::hasColumn('subcategories', 'how_to_redeem_ar')) {
                $table->text('how_to_redeem_ar')->nullable()->after('how_to_redeem_en');
            }
        });

        Schema::table('products', function (Blueprint $table): void {
            if (! Schema::hasColumn('products', 'how_to_redeem_en')) {
                $table->text('how_to_redeem_en')->nullable()->after('description_en');
            }

            if (! Schema::hasColumn('products', 'how_to_redeem_ar')) {
                $table->text('how_to_redeem_ar')->nullable()->after('how_to_redeem_en');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $drops = array_values(array_filter([
                Schema::hasColumn('products', 'how_to_redeem_en') ? 'how_to_redeem_en' : null,
                Schema::hasColumn('products', 'how_to_redeem_ar') ? 'how_to_redeem_ar' : null,
            ]));

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });

        Schema::table('subcategories', function (Blueprint $table): void {
            $drops = array_values(array_filter([
                Schema::hasColumn('subcategories', 'how_to_redeem_en') ? 'how_to_redeem_en' : null,
                Schema::hasColumn('subcategories', 'how_to_redeem_ar') ? 'how_to_redeem_ar' : null,
            ]));

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });

        Schema::table('users', function (Blueprint $table): void {
            $drops = array_values(array_filter([
                Schema::hasColumn('users', 'last_login_at') ? 'last_login_at' : null,
                Schema::hasColumn('users', 'password_reset_code') ? 'password_reset_code' : null,
                Schema::hasColumn('users', 'password_reset_expires_at') ? 'password_reset_expires_at' : null,
            ]));

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });
    }
};
