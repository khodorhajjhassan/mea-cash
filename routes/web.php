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
use App\Http\Controllers\Admin\Web\HomepageSectionController;
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
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Storefront\StorefrontController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\ContactController as StorefrontContactController;
use App\Http\Controllers\Storefront\CustomerDashboardController;
use App\Http\Controllers\Storefront\CustomerAuthController;
use App\Http\Controllers\Storefront\NotificationController as StorefrontNotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Localized Storefront Routes
|--------------------------------------------------------------------------
*/
Route::redirect('/', '/en');

Route::prefix('{locale}')
    ->whereIn('locale', ['en', 'ar'])
    ->group(function (): void {
        // Home
        Route::get('/', [StorefrontController::class, 'index'])->name('store.home');

        // Public pages
        Route::get('/category/{slug}', [StorefrontController::class, 'category'])->name('store.category');
        Route::get('/pages/{slug}', [StorefrontController::class, 'localizedPage'])->name('store.page');
        Route::get('/contact', [StorefrontContactController::class, 'create'])->name('store.contact');
        Route::post('/contact', [StorefrontContactController::class, 'store'])
            ->middleware('throttle:3,10')
            ->name('store.contact.store');
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
            Route::get('/register/verify', [CustomerAuthController::class, 'showVerifyOtp'])->name('store.register.verify');
            Route::post('/register/verify', [CustomerAuthController::class, 'verifyOtp'])->name('store.register.verify.store');
            Route::post('/register/resend', [CustomerAuthController::class, 'resendOtp'])->name('store.register.resend');
        });
        Route::post('/customer/logout', [CustomerAuthController::class, 'logout'])->name('store.logout')->middleware('auth');

        Route::prefix('auth')->group(function (): void {
            Route::get('login', [UserAuthController::class, 'create'])->name('login');
            Route::post('login', [UserAuthController::class, 'store'])->name('login.store');
            Route::post('logout', [UserAuthController::class, 'destroy'])->name('logout');
        });

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
                Route::post('/orders/{order}/feedback', [CustomerDashboardController::class, 'submitFeedback'])->name('orders.feedback');
                Route::post('/orders/{order}/report', [CustomerDashboardController::class, 'submitReport'])->name('orders.report');
                Route::get('/orders/{orderNumber}', [CustomerDashboardController::class, 'orderDetail'])->name('orders.detail');
                Route::get('/wallet', [CustomerDashboardController::class, 'wallet'])->name('wallet');
                Route::post('/wallet/topup', [CustomerDashboardController::class, 'submitTopup'])->name('wallet.topup');
                Route::get('/profile', [CustomerDashboardController::class, 'profile'])->name('profile');
                Route::put('/profile', [CustomerDashboardController::class, 'updateProfile'])->name('profile.update');
                Route::get('/notifications/{id}/read', [StorefrontNotificationController::class, 'read'])->name('notifications.read');
                Route::post('/notifications/read-all', [StorefrontNotificationController::class, 'readAll'])->name('notifications.read-all');
            });

        // Admin Routes (Localized)
        Route::prefix('admin')
            ->name('admin.')
            ->group(function (): void {
                Route::post('logout', [UserAuthController::class, 'destroy'])->name('logout');

                Route::middleware(['auth', 'admin'])->group(function (): void {
                    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

                    Route::resource('categories', CategoryController::class)
                        ->middleware('permission:categories.index')
                        ->middlewareFor(['create', 'store'], 'permission:categories.create')
                        ->middlewareFor(['edit', 'update'], 'permission:categories.edit')
                        ->middlewareFor('destroy', 'permission:categories.delete');
                    Route::resource('subcategories', SubcategoryController::class)
                        ->middleware('permission:subcategories.index')
                        ->middlewareFor(['create', 'store'], 'permission:subcategories.create')
                        ->middlewareFor(['edit', 'update'], 'permission:subcategories.edit')
                        ->middlewareFor('destroy', 'permission:subcategories.delete');
                    Route::resource('product-types', ProductTypeController::class)
                        ->middleware('permission:product-types.index')
                        ->middlewareFor(['create', 'store'], 'permission:product-types.create')
                        ->middlewareFor(['edit', 'update'], 'permission:product-types.edit')
                        ->middlewareFor('destroy', 'permission:product-types.delete');
                    Route::resource('products', ProductController::class)
                        ->middleware('permission:products.index')
                        ->middlewareFor(['create', 'store'], 'permission:products.create')
                        ->middlewareFor(['edit', 'update'], 'permission:products.edit')
                        ->middlewareFor('destroy', 'permission:products.delete');
                    
                    Route::get('orders/pending', [OrderController::class, 'pending'])->middleware('permission:orders.pending')->name('orders.pending');
                    Route::resource('orders', OrderController::class)->only(['index', 'show'])->middleware('permission:orders.index');
                    Route::match(['post', 'put'], 'orders/{order}/status', [OrderController::class, 'updateStatus'])->middleware('permission:orders.edit')->name('orders.status');
                    Route::post('orders/{order}/refund', [OrderController::class, 'refund'])->middleware('permission:orders.edit')->name('orders.refund');
                    Route::post('orders/{order}/fulfill', [OrderController::class, 'fulfill'])->middleware('permission:orders.edit')->name('orders.fulfill');
                    Route::post('orders/{order}/fail', [OrderController::class, 'fail'])->middleware('permission:orders.edit')->name('orders.fail');

                    Route::resource('topups', TopupController::class)
                        ->only(['index', 'show'])
                        ->middleware('permission:topups.index')
                        ->middlewareFor('show', 'permission:topups.show');
                    Route::post('topups/{topup}/approve', [TopupController::class, 'approve'])->middleware('permission:topups.approve')->name('topups.approve');
                    Route::post('topups/{topup}/reject', [TopupController::class, 'reject'])->middleware('permission:topups.reject')->name('topups.reject');

                    Route::get('transactions/user/{user}', [TransactionController::class, 'user'])->middleware('permission:transactions.index')->name('transactions.user');
                    Route::resource('transactions', TransactionController::class)
                        ->only(['index', 'show'])
                        ->middleware('permission:transactions.index')
                        ->middlewareFor('show', 'permission:transactions.show');
                    Route::post('users/{user}/credit', [UserController::class, 'credit'])->middleware('permission:users.credit-wallet')->name('users.credit');
                    Route::resource('users', UserController::class)
                        ->middleware('permission:users.index')
                        ->middlewareFor(['edit', 'update'], 'permission:users.edit');

                    Route::post('payment-methods/{paymentMethod}/toggle', [PaymentMethodController::class, 'toggle'])
                        ->middleware('permission:payment-methods.edit')
                        ->name('payment-methods.toggle');
                    Route::resource('payment-methods', PaymentMethodController::class)
                        ->only(['index', 'update'])
                        ->middleware('permission:payment-methods.index')
                        ->middlewareFor('update', 'permission:payment-methods.edit');
                    Route::resource('suppliers', SupplierController::class)
                        ->except(['show'])
                        ->middleware('permission:suppliers.index')
                        ->middlewareFor(['create', 'store'], 'permission:suppliers.create')
                        ->middlewareFor(['edit', 'update'], 'permission:suppliers.edit')
                        ->middlewareFor('destroy', 'permission:suppliers.delete');
                    Route::get('analytics', [AnalyticsController::class, 'index'])->middleware('permission:analytics.index')->name('analytics.index');
                    Route::get('analytics/revenue', [AnalyticsController::class, 'revenue'])->middleware('permission:analytics.index')->name('analytics.revenue');
                    Route::get('analytics/products', [AnalyticsController::class, 'products'])->middleware('permission:analytics.index')->name('analytics.products');
                    Route::get('analytics/users', [AnalyticsController::class, 'users'])->middleware('permission:analytics.index')->name('analytics.users');
                    Route::get('analytics/profit', [AnalyticsController::class, 'profit'])->middleware('permission:analytics.index')->name('analytics.profit');
                    Route::resource('contact', ContactController::class)
                        ->only(['index', 'show', 'destroy'])
                        ->middleware('permission:contact.index')
                        ->middlewareFor('show', 'permission:contact.show')
                        ->middlewareFor('destroy', 'permission:contact.delete');
                    Route::resource('feedback', FeedbackController::class)
                        ->only(['index', 'show', 'destroy'])
                        ->middleware('permission:feedback.index')
                        ->middlewareFor('show', 'permission:feedback.show')
                        ->middlewareFor('destroy', 'permission:feedback.delete');
                    Route::post('feedback/{feedback}/status', [FeedbackController::class, 'updateStatus'])->middleware('permission:feedback.edit')->name('feedback.status');
                    Route::post('feedback/{feedback}/toggle-featured', [FeedbackController::class, 'toggleFeatured'])->middleware('permission:feedback.edit')->name('feedback.toggle-featured');
                    Route::get('pages/edit', [PageController::class, 'edit'])->middleware('permission:pages.edit')->name('pages.edit');
                    Route::post('pages', [PageController::class, 'update'])->middleware('permission:pages.edit')->name('pages.update');
                    
                    Route::get('settings', [SettingController::class, 'index'])->middleware('permission:settings.general')->name('settings.index');
                    Route::get('settings/general', [SettingController::class, 'general'])->middleware('permission:settings.general')->name('settings.general');
                    Route::get('settings/seo', [SettingController::class, 'seo'])->middleware('permission:settings.general')->name('settings.seo');
                    Route::post('settings', [SettingController::class, 'update'])->middleware('permission:settings.general')->name('settings.update');
                    Route::post('settings/seo', [SettingController::class, 'updateSeo'])->middleware('permission:settings.general')->name('settings.seo.update');

                    Route::get('notifications', [NotificationController::class, 'index'])->middleware('permission:notifications.index')->name('notifications.index');
                    Route::get('notifications/{id}/read', [NotificationController::class, 'read'])->middleware('permission:notifications.index')->name('notifications.read');
                    Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->middleware('permission:notifications.index')->name('notifications.read-all');

                    Route::get('roles', [RoleController::class, 'index'])->middleware(['role:super-admin', 'permission:roles.index'])->name('roles.index');
                    Route::get('roles/create', [RoleController::class, 'create'])->middleware(['role:super-admin', 'permission:roles.create'])->name('roles.create');
                    Route::post('roles', [RoleController::class, 'store'])->middleware(['role:super-admin', 'permission:roles.create'])->name('roles.store');
                    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->middleware(['role:super-admin', 'permission:roles.edit'])->name('roles.edit');
                    Route::put('roles/{role}', [RoleController::class, 'update'])->middleware(['role:super-admin', 'permission:roles.edit'])->name('roles.update');
                    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->middleware(['role:super-admin', 'permission:roles.delete'])->name('roles.destroy');

                    Route::get('roles/assignments', [RoleController::class, 'assignments'])->middleware(['role:super-admin', 'permission:roles.assign'])->name('roles.assignments');
                    Route::put('roles/assignments/{user}', [RoleController::class, 'updateAssignments'])->middleware(['role:super-admin', 'permission:roles.assign'])->name('roles.assignments.update');

                    Route::post('products/{product}/packages', [ProductController::class, 'storePackage'])->middleware('permission:products.edit')->name('products.packages.store');
                    Route::put('products/packages/{package}', [ProductController::class, 'updatePackage'])->middleware('permission:products.edit')->name('products.packages.update');
                    Route::post('products/{product}/fields', [ProductController::class, 'storeField'])->middleware('permission:products.edit')->name('products.fields.store');
                    Route::resource('banners', BannerController::class)
                        ->except(['show'])
                        ->middleware('permission:banners.index')
                        ->middlewareFor(['create', 'store'], 'permission:banners.create')
                        ->middlewareFor(['edit', 'update'], 'permission:banners.edit')
                        ->middlewareFor('destroy', 'permission:banners.delete');
                    Route::resource('faqs', FaqController::class)
                        ->except(['show'])
                        ->middleware('permission:faqs.index')
                        ->middlewareFor(['create', 'store'], 'permission:faqs.create')
                        ->middlewareFor(['edit', 'update'], 'permission:faqs.edit')
                        ->middlewareFor('destroy', 'permission:faqs.delete');
                    Route::resource('homepage-sections', HomepageSectionController::class)
                        ->except(['show'])
                        ->middleware('permission:homepage-sections.index')
                        ->middlewareFor(['create', 'store'], 'permission:homepage-sections.create')
                        ->middlewareFor(['edit', 'update'], 'permission:homepage-sections.edit')
                        ->middlewareFor('destroy', 'permission:homepage-sections.delete');

                    Route::post('categories/reorder', [CategoryController::class, 'reorder'])->middleware('permission:categories.edit')->name('categories.reorder');
                    Route::post('products/reorder', [ProductController::class, 'reorder'])->middleware('permission:products.edit')->name('products.reorder');

                    Route::post('products/{product}/duplicate', [ProductController::class, 'duplicate'])->middleware('permission:products.create')->name('products.duplicate');
                });
            });
    });

// Admin API Routes (Un-localized is fine for API)
Route::prefix('admin/api')
    ->name('admin.api.')
    ->middleware(['auth', 'admin'])
    ->group(function (): void {
        Route::apiResource('categories', ApiCategoryController::class);
        Route::apiResource('subcategories', ApiSubcategoryController::class);
        Route::apiResource('products', ApiProductController::class);
        Route::apiResource('product-packages', ApiProductPackageController::class);
    });

Route::get('/{path}', function (string $path) {
    return redirect('/en/' . ltrim($path, '/'));
})->where('path', '^(?!en(?:/|$)|ar(?:/|$)|up$).+');
