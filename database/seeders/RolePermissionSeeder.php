<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    private const GUARD = 'web';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 1. Define Permissions
        $permissions = [
            // Catalog
            'categories.index', 'categories.create', 'categories.edit', 'categories.delete',
            'subcategories.index', 'subcategories.create', 'subcategories.edit', 'subcategories.delete',
            'products.index', 'products.create', 'products.edit', 'products.delete', 'products.toggle-availability',
            'codes.index', 'codes.import', 'codes.delete',

            // Orders
            'orders.index', 'orders.show', 'orders.update-status', 'orders.fulfill', 'orders.refund', 'orders.pending',

            // Wallet
            'topups.index', 'topups.show', 'topups.approve', 'topups.reject',
            'transactions.index', 'transactions.adjust',

            // Users
            'users.index', 'users.show', 'users.edit', 'users.toggle-status', 'users.credit-wallet', 'users.vip',

            // Finance
            'payment-methods.index', 'payment-methods.edit', 'payment-methods.toggle',
            'suppliers.index', 'suppliers.create', 'suppliers.edit', 'suppliers.delete',

            // Analytics
            'analytics.index', 'analytics.export',

            // Support
            'contact.index', 'contact.show', 'contact.delete',
            'feedback.index', 'feedback.show', 'feedback.delete',

            // Settings
            'settings.general', 'settings.seo', 'settings.payment', 'settings.appearance', 'settings.security',

            // Roles
            'roles.index', 'roles.create', 'roles.edit', 'roles.delete', 'roles.assign',
        ];

        $now = now();
        Permission::query()->upsert(
            collect($permissions)->map(fn (string $permission) => [
                'name' => $permission,
                'guard_name' => self::GUARD,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all(),
            ['name', 'guard_name'],
            ['updated_at']
        );

        // Ensure new permissions are available to sync operations.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissionModels = Permission::query()
            ->where('guard_name', self::GUARD)
            ->whereIn('name', $permissions)
            ->get()
            ->keyBy('name');

        // 2. Create Roles and Assign Permissions

        // Super Admin
        Role::findOrCreate('super-admin', self::GUARD);
        // Note: Super Admin permissions are handled via Gate::before in AppServiceProvider

        // Admin
        $admin = Role::findOrCreate('admin', self::GUARD);
        $adminPermissionNames = collect($permissions)->filter(fn ($p) => !in_array($p, [
            'settings.security',
            'roles.delete',
            'transactions.adjust'
        ]))->values();
        $admin->syncPermissions($permissionModels->only($adminPermissionNames->all())->values());

        // Accountant
        $accountant = Role::findOrCreate('accountant', self::GUARD);
        $accountantPermissionNames = collect([
            'orders.index',
            'orders.show',
            'transactions.index',
            'analytics.index',
            'analytics.export',
            'topups.index',
            'topups.show',
        ]);
        $accountant->syncPermissions($permissionModels->only($accountantPermissionNames->all())->values());

        // 3. Create Initial Super Admin User (Idempotent)
        $email = strtolower((string) env('SUPER_ADMIN_EMAIL', 'admin@meacash.com'));
        $password = (string) env('SUPER_ADMIN_PASSWORD', 'password');

        if (! User::where('email', $email)->exists()) {
            $user = User::create([
                'name' => 'System Admin',
                'email' => $email,
                'phone' => '00000000',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'preferred_language' => 'en',
                'is_active' => true,
                'is_admin' => true,
            ]);

            $user->assignRole('super-admin');
        } else {
            $user = User::where('email', $email)->first();
            if ($user) {
                if (! $user->is_admin || ! $user->is_active) {
                    $user->forceFill([
                        'is_admin' => true,
                        'is_active' => true,
                    ])->save();
                }

                if (! $user->hasRole('super-admin')) {
                    $user->assignRole('super-admin');
                }
            }
        }
    }
}
