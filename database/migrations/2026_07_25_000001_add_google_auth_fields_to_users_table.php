<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'auth_provider')) {
                $table->string('auth_provider', 30)->nullable()->after('password');
            }

            if (! Schema::hasColumn('users', 'provider_id')) {
                $table->string('provider_id')->nullable()->unique()->after('auth_provider');
            }

            if (! Schema::hasColumn('users', 'avatar_url')) {
                $table->string('avatar_url')->nullable()->after('provider_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $drops = array_values(array_filter([
                Schema::hasColumn('users', 'auth_provider') ? 'auth_provider' : null,
                Schema::hasColumn('users', 'provider_id') ? 'provider_id' : null,
                Schema::hasColumn('users', 'avatar_url') ? 'avatar_url' : null,
            ]));

            if ($drops !== []) {
                if (Schema::hasColumn('users', 'provider_id')) {
                    $table->dropUnique('users_provider_id_unique');
                }
                $table->dropColumn($drops);
            }
        });
    }
};
