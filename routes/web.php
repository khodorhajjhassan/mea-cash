<?php

use App\Http\Controllers\Admin\CategoryController as ApiCategoryController;
use App\Http\Controllers\Admin\ProductController as ApiProductController;
use App\Http\Controllers\Admin\ProductPackageController as ApiProductPackageController;
use App\Http\Controllers\Admin\SubcategoryController as ApiSubcategoryController;
use App\Http\Controllers\Admin\Web\BannerController;
use App\Http\Controllers\Admin\Web\FaqController;
use App\Http\Controllers\Admin\Web\CategoryController;
use App\Http\Controllers\Admin\Web\CodeController;
use App\Http\Controllers\Admin\Web\ContactController;
use App\Http\Controllers\Admin\Web\DashboardController;
use App\Http\Controllers\Admin\Web\FeedbackController;
use App\Http\Controllers\Admin\Web\OrderController;
use App\Http\Controllers\Admin\Web\PaymentMethodController;
use App\Http\Controllers\Admin\Web\ProductController;
use App\Http\Controllers\Admin\Web\ProductPackageController;
use App\Http\Controllers\Admin\Web\ProductTypeController;
use App\Http\Controllers\Admin\Web\SettingController;
use App\Http\Controllers\Admin\Web\SubcategoryController;
use App\Http\Controllers\Admin\Web\SupplierController;
use App\Http\Controllers\Admin\Web\TopupController;
use App\Http\Controllers\Admin\Web\TransactionController;
use App\Http\Controllers\Admin\Web\UserController;
use App\Http\Controllers\Admin\Web\AnalyticsController;
use App\Http\Controllers\Admin\Web\NotificationController;
use App\Http\Controllers\Admin\Web\PageController;
use App\Http\Controllers\Admin\Web\RoleController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Storefront\StorefrontController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\CustomerDashboardController;
use App\Http\Controllers\Storefront\CustomerAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront (Public) Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [StorefrontController::class, 'index'])->name('store.home');
Route::get('/category/{slug}', [StorefrontController::class, 'category'])->name('store.category');
Route::get('/search', [StorefrontController::class, 'search'])->name('store.search');
Route::get('/api/product/{slug}', [StorefrontController::class, 'subcategoryJson'])->name('store.product.json');
Route::get('/api/subcategory/{slug}', [StorefrontController::class, 'subcategoryJson'])->name('store.subcategory.json');
Route::get('/api/search', [StorefrontController::class, 'search'])->name('store.search.api');

// Cart
Route::post('/cart/add', [CartController::class, 'add'])->middleware(['auth', 'customer'])->name('store.cart.add');
Route::get('/cart', [CartController::class, 'show'])->name('store.cart');
Route::delete('/cart/{itemId}', [CartController::class, 'remove'])->name('store.cart.remove');
Route::delete('/cart', [CartController::class, 'clear'])->name('store.cart.clear');

// Customer Auth
Route::middleware('guest')->group(function (): void {
    Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('store.register');
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('store.register.store');
});
Route::post('/customer/logout', [CustomerAuthController::class, 'logout'])->name('store.logout')->middleware('auth');

// Checkout (auth required)
Route::middleware(['auth', 'customer'])->group(function (): void {
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('store.checkout');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('store.checkout.process');
    Route::get('/order/{orderNumber}/confirmation', [CheckoutController::class, 'confirmation'])->name('store.confirmation');
});

// Customer Dashboard (auth required)
Route::prefix('dashboard')
    ->name('store.')
    ->middleware(['auth', 'customer'])
    ->group(function (): void {
        Route::get('/', [CustomerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders', [CustomerDashboardController::class, 'orders'])->name('orders');
        Route::get('/orders/{orderNumber}', [CustomerDashboardController::class, 'orderDetail'])->name('orders.detail');
        Route::get('/wallet', [CustomerDashboardController::class, 'wallet'])->name('wallet');
        Route::post('/wallet/topup', [CustomerDashboardController::class, 'submitTopup'])->name('wallet.topup');
        Route::get('/profile', [CustomerDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [CustomerDashboardController::class, 'updateProfile'])->name('profile.update');
    });

Route::prefix('auth')
    ->group(function (): void {
        Route::get('login', [UserAuthController::class, 'create'])->name('login');
        Route::post('login', [UserAuthController::class, 'store'])->name('login.store');
        Route::post('logout', [UserAuthController::class, 'destroy'])->name('logout');
    });

Route::prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('login', [AdminAuthController::class, 'create'])->name('login');
        Route::post('login', [AdminAuthController::class, 'store'])->name('login.store');
        Route::post('logout', [AdminAuthController::class, 'destroy'])->name('logout');

        Route::middleware(['auth', 'admin'])->group(function (): void {
            Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

            Route::resource('categories', CategoryController::class)->middleware('permission:categories.index');
            Route::resource('subcategories', SubcategoryController::class)->middleware('permission:categories.index');
            Route::resource('product-types', ProductTypeController::class)->middleware('permission:categories.index');
            Route::resource('products', ProductController::class)->middleware('permission:products.index');
            
            Route::get('orders/pending', [OrderController::class, 'pending'])->middleware('permission:orders.pending')->name('orders.pending');
            Route::resource('orders', OrderController::class)->middleware('permission:orders.index');
            Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->middleware('permission:orders.edit')->name('orders.status');
            Route::post('orders/{order}/refund', [OrderController::class, 'refund'])->middleware('permission:orders.edit')->name('orders.refund');
            Route::post('orders/{order}/fulfill', [OrderController::class, 'fulfill'])->middleware('permission:orders.edit')->name('orders.fulfill');
            Route::post('orders/{order}/fail', [OrderController::class, 'fail'])->middleware('permission:orders.edit')->name('orders.fail');

            Route::resource('topups', TopupController::class)->middleware('permission:topups.index');
            Route::post('topups/{topup}/approve', [TopupController::class, 'approve'])->middleware('permission:topups.approve')->name('topups.approve');
            Route::post('topups/{topup}/reject', [TopupController::class, 'reject'])->middleware('permission:topups.reject')->name('topups.reject');

            Route::resource('transactions', TransactionController::class)->middleware('permission:transactions.index');
            Route::post('users/{user}/credit', [UserController::class, 'credit'])->middleware('permission:users.credit-wallet')->name('users.credit');
            Route::resource('users', UserController::class)->middleware('permission:users.index');

            Route::resource('payment-methods', PaymentMethodController::class)->middleware('permission:payment-methods.index');
            Route::resource('suppliers', SupplierController::class)->middleware('permission:suppliers.index');
            Route::resource('analytics', AnalyticsController::class)->middleware('permission:analytics.index');
            Route::resource('contact', ContactController::class)->middleware('permission:contact.index');
            Route::resource('feedback', FeedbackController::class)->middleware('permission:feedback.index');
            Route::resource('pages', PageController::class)->middleware('permission:settings.general');
            
            Route::get('settings', [SettingController::class, 'index'])->middleware('permission:settings.general')->name('settings.index');
            Route::post('settings', [SettingController::class, 'update'])->middleware('permission:settings.general')->name('settings.update');
            Route::post('settings/seo', [SettingController::class, 'updateSeo'])->middleware('permission:settings.general')->name('settings.seo');

            Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
            Route::get('notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
            Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');

            Route::get('roles', [RoleController::class, 'index'])->middleware('permission:roles.index')->name('roles.index');
            Route::get('roles/create', [RoleController::class, 'create'])->middleware('permission:roles.create')->name('roles.create');
            Route::post('roles', [RoleController::class, 'store'])->middleware('permission:roles.create')->name('roles.store');
            Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->middleware('permission:roles.edit')->name('roles.edit');
            Route::put('roles/{role}', [RoleController::class, 'update'])->middleware('permission:roles.edit')->name('roles.update');
            Route::delete('roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:roles.delete')->name('roles.destroy');

            Route::get('roles/assignments', [RoleController::class, 'assignments'])->middleware('permission:roles.assign')->name('roles.assignments');
            Route::put('roles/assignments/{user}', [RoleController::class, 'updateAssignments'])->middleware('permission:roles.assign')->name('roles.assignments.update');

            Route::post('products/{product}/packages', [ProductController::class, 'storePackage'])->middleware('permission:products.edit')->name('products.packages.store');
            Route::put('products/packages/{package}', [ProductController::class, 'updatePackage'])->middleware('permission:products.edit')->name('products.packages.update');
            Route::post('products/{product}/fields', [ProductController::class, 'storeField'])->middleware('permission:products.edit')->name('products.fields.store');
            Route::resource('banners', BannerController::class)->middleware('permission:categories.index');
            Route::resource('faqs', FaqController::class)->middleware('permission:categories.index');

            Route::post('categories/reorder', [CategoryController::class, 'reorder'])->middleware('permission:categories.index')->name('categories.reorder');
            Route::post('products/reorder', [ProductController::class, 'reorder'])->middleware('permission:products.index')->name('products.reorder');

            Route::post('products/{product}/duplicate', [ProductController::class, 'duplicate'])->middleware('permission:products.create')->name('products.duplicate');
        });
    });

Route::prefix('admin/api')
    ->name('admin.api.')
    ->middleware(['auth', 'admin'])
    ->group(function (): void {
        Route::apiResource('categories', ApiCategoryController::class);
        Route::apiResource('subcategories', ApiSubcategoryController::class);
        Route::apiResource('products', ApiProductController::class);
        Route::apiResource('product-packages', ApiProductPackageController::class);
    });
