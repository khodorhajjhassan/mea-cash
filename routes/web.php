<?php

use App\Http\Controllers\Admin\CategoryController as ApiCategoryController;
use App\Http\Controllers\Admin\ProductController as ApiProductController;
use App\Http\Controllers\Admin\ProductPackageController as ApiProductPackageController;
use App\Http\Controllers\Admin\SubcategoryController as ApiSubcategoryController;
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
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\UserAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [UserAuthController::class, 'create'])->name('login');
    Route::post('/login', [UserAuthController::class, 'store'])->name('login.store');

    Route::get('/admin/login', [AdminAuthController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'store'])->name('admin.login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [UserAuthController::class, 'destroy'])->name('logout');
    Route::post('/admin/logout', [AdminAuthController::class, 'destroy'])->name('admin.logout');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::resource('categories', CategoryController::class);
        Route::resource('subcategories', SubcategoryController::class);
        Route::resource('product-types', ProductTypeController::class);
        Route::resource('products', ProductController::class);
        Route::resource('product-packages', ProductPackageController::class);

        Route::get('codes', [CodeController::class, 'index'])->name('codes.index');
        Route::get('codes/import', [CodeController::class, 'import'])->name('codes.import');
        Route::post('codes/import', [CodeController::class, 'importStore'])->name('codes.import.store');
        Route::post('codes', [CodeController::class, 'store'])->name('codes.store');
        Route::delete('codes/{code}', [CodeController::class, 'destroy'])->name('codes.destroy');

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/pending', [OrderController::class, 'pending'])->name('orders.pending');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::put('orders/{order}/status', [OrderController::class, 'status'])->name('orders.status');
        Route::post('orders/{order}/refund', [OrderController::class, 'refund'])->name('orders.refund');
        Route::post('orders/{order}/fulfill', [OrderController::class, 'fulfill'])->name('orders.fulfill');
        Route::post('orders/{order}/fail', [OrderController::class, 'fail'])->name('orders.fail');

        Route::get('topups', [TopupController::class, 'index'])->name('topups.index');
        Route::get('topups/{topup}', [TopupController::class, 'show'])->name('topups.show');
        Route::post('topups/{topup}/approve', [TopupController::class, 'approve'])->name('topups.approve');
        Route::post('topups/{topup}/reject', [TopupController::class, 'reject'])->name('topups.reject');

        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('transactions/{user}', [TransactionController::class, 'user'])->name('transactions.user');
        Route::post('transactions/adjust', [TransactionController::class, 'adjust'])->name('transactions.adjust');

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/vip', [UserController::class, 'vip'])->name('users.vip');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
        Route::post('users/{user}/credit', [UserController::class, 'credit'])->name('users.credit');

        Route::get('payment-methods', [PaymentMethodController::class, 'index'])->name('payment-methods.index');
        Route::put('payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update'])->name('payment-methods.update');
        Route::post('payment-methods/{paymentMethod}/toggle', [PaymentMethodController::class, 'toggle'])->name('payment-methods.toggle');

        Route::resource('suppliers', SupplierController::class)->except(['show']);

        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('analytics/revenue', [AnalyticsController::class, 'revenue'])->name('analytics.revenue');
        Route::get('analytics/products', [AnalyticsController::class, 'products'])->name('analytics.products');
        Route::get('analytics/profit', [AnalyticsController::class, 'profit'])->name('analytics.profit');
        Route::get('analytics/users', [AnalyticsController::class, 'users'])->name('analytics.users');

        Route::get('contact', [ContactController::class, 'index'])->name('contact.index');
        Route::get('contact/{contact}', [ContactController::class, 'show'])->name('contact.show');
        Route::delete('contact/{contact}', [ContactController::class, 'destroy'])->name('contact.destroy');

        Route::get('feedback', [FeedbackController::class, 'index'])->name('feedback.index');
        Route::get('feedback/{feedback}', [FeedbackController::class, 'show'])->name('feedback.show');
        Route::delete('feedback/{feedback}', [FeedbackController::class, 'destroy'])->name('feedback.destroy');

        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('settings/payment', [SettingController::class, 'payment'])->name('settings.payment');
        Route::get('settings/seo', [SettingController::class, 'seo'])->name('settings.seo');

        Route::post('products/{product}/packages', [ProductController::class, 'storePackage'])->name('products.packages.store');
        Route::put('products/packages/{package}', [ProductController::class, 'updatePackage'])->name('products.packages.update');
        Route::post('products/{product}/fields', [ProductController::class, 'storeField'])->name('products.fields.store');
        Route::post('products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
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
