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
        $permissionGroups = [
            'categories' => ['index', 'create', 'edit', 'delete'],
            'subcategories' => ['index', 'create', 'edit', 'delete'],
            'product-types' => ['index', 'create', 'edit', 'delete'],
            'products' => ['index', 'create', 'edit', 'delete', 'toggle-availability'],
            'codes' => ['index', 'import', 'delete'],
            'orders' => ['index', 'show', 'edit', 'pending', 'refund', 'fulfill', 'fail', 'update-status'],
            'topups' => ['index', 'show', 'approve', 'reject'],
            'transactions' => ['index', 'show', 'adjust'],
            'users' => ['index', 'show', 'edit', 'toggle-status', 'credit-wallet', 'vip'],
            'payment-methods' => ['index', 'edit', 'toggle'],
            'suppliers' => ['index', 'create', 'edit', 'delete'],
            'analytics' => ['index', 'export'],
            'contact' => ['index', 'show', 'delete'],
            'feedback' => ['index', 'show', 'edit', 'delete'],
            'homepage-sections' => ['index', 'create', 'edit', 'delete'],
            'banners' => ['index', 'create', 'edit', 'delete'],
            'faqs' => ['index', 'create', 'edit', 'delete'],
            'pages' => ['edit'],
            'notifications' => ['index'],
            'settings' => ['general', 'seo', 'payment', 'appearance', 'security'],
            'roles' => ['index', 'create', 'edit', 'delete', 'assign'],
        ];

        $permissions = collect($permissionGroups)
            ->flatMap(fn (array $actions, string $group) => collect($actions)->map(
                fn (string $action) => "{$group}.{$action}"
            ))
            ->values()
            ->all();

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
            'transactions.adjust'
        ]) && !str_starts_with($p, 'roles.'))->values();
        $admin->syncPermissions($permissionModels->only($adminPermissionNames->all())->values());

        // Accountant
        $accountant = Role::findOrCreate('accountant', self::GUARD);
        $accountantPermissionNames = collect([
            'orders.index',
            'orders.show',
            'transactions.index',
            'transactions.show',
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
