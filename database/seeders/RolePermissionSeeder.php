<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

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

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // 2. Create Roles and Assign Permissions

        // Super Admin
        Role::findOrCreate('super-admin');
        // Note: Super Admin permissions are handled via Gate::before in AppServiceProvider

        // Admin
        $admin = Role::findOrCreate('admin');
        $adminPermissions = collect($permissions)->filter(fn($p) => !in_array($p, [
            'settings.security',
            'roles.delete',
            'transactions.adjust'
        ]))->toArray();
        $admin->syncPermissions($adminPermissions);

        // Accountant
        $accountant = Role::findOrCreate('accountant');
        $accountant->syncPermissions([
            'orders.index',
            'orders.show',
            'transactions.index',
            'analytics.index',
            'analytics.export',
            'topups.index',
            'topups.show'
        ]);

        // 3. Create Initial Super Admin User (Idempotent)
        $email = env('SUPER_ADMIN_EMAIL', 'admin@meacash.com');
        $password = env('SUPER_ADMIN_PASSWORD');

        if (! User::where('email', $email)->exists() && $password) {
            $user = User::create([
                'name' => 'System Admin',
                'email' => $email,
                'phone' => '00000000',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'preferred_language' => 'en',
                'is_active' => true,
            ]);

            $user->assignRole('super-admin');
        } else {
            $user = User::where('email', $email)->first();
            if ($user && !$user->hasRole('super-admin')) {
                $user->assignRole('super-admin');
            }
        }
    }
}
