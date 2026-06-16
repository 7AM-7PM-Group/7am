<?php

use App\Http\Controllers\ApiTestController;
use App\Http\Controllers\JurnalAuthController;
use App\Livewire\EsbLogIndex;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


Volt::route('/', 'home')->name('home');
Volt::route('reservation', 'reservation-create')->name('reservation');
Volt::route('loyality-card', 'loyality-home')->name('loyality.home');

Volt::route('promotions', 'promotion')->name('promotion');

Route::middleware(['auth', 'verified'])->group(function () {
    Volt::route('settings/profile', 'profile')->name('settings.profile');
    Volt::route('settings/address', 'address')->name('settings.address');
    Volt::route('settings/password', 'password')->name('settings.password');
    Volt::route('settings/appearance', 'appearence')->name('settings.appearance');
});
Route::middleware(['auth', 'verified', 'customer'])->group(function () {
    Volt::route('reservation/make', 'reservation-create')->name('reservation.create');
    Volt::route('reservation/history', 'reservation.history')->name('reservation.history');
    Volt::route('settings/Business-info', 'Business-info')->name('settings.Business-info');
});
Route::middleware(['auth', 'verified', 'outlet-admin'])->group(function () {
    Volt::route('reservation/', 'reservation-index')->name('reservation.index');

    Volt::route('members', 'member-index')->name('member.index');
    Volt::route('members/{slug}', 'member-show')->name('member.show');
});
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    // Volt::route('set-categories', 'set-category-index')->name('setcategory.index');

    Volt::route('outlets', 'outlet-index')->name('outlet.index');
    Volt::route('outlets/add', 'outlet-create')->name('outlet.create');
    Volt::route('outlets/{slug}/edit', 'outlet-create')->name('outlet.edit');

    Volt::route('types', 'type-index')->name('type.index');
    Volt::route('cards', 'card-index')->name('card.index');
    Volt::route('admins', 'admin-index')->name('admin.index');
    Route::get('esb-logs', EsbLogIndex::class)->name('esb.logs.index');

    Volt::route('redeem-reward', 'redeem-index')->name('redeem.index');
    Volt::route('our-promotions', 'promotion-index')->name('promotion.index');
});

Route::prefix('b2b')->group(function () {
    Volt::route('/', 'b2b-home')->name('b2b-home');
    Volt::route('contact', 'contact')->name('contact');

    Volt::route('shop', 'shop-index')->name('shop.index');
    Volt::route('shop/{slug}', 'shop-show')->name('shop.show');

    Volt::route('checkout/{slug}', 'checkout')->name('checkout');
    Volt::route('invoice/{slug}', 'invoice')->name('invoice');

    Volt::route('wholesale', 'wholesale-index')->name('wholesale.index');
});
Route::prefix('b2b')->middleware(['auth', 'verified', 'sales-admin'])->group(function () {
    Volt::route('dashboard', 'dashboard')
        ->name('dashboard');
    Volt::route('products', 'product-index')->name('product.index');

    Volt::route('coupons', 'coupon-index')->name('coupon.index');
    Volt::route('coupons/add', 'coupon-create')->name('coupon.create');
    Volt::route('coupons/{slug}/edit', 'coupon-create')->name('coupon.edit');

    Volt::route('minimum-orders', 'minimum-order-index')->name('minimum-order.index');

    Volt::route('categories', 'category-index')->name('category.index');

    Volt::route('businesses', 'business-index')->name('business.index');

    Volt::route('transaction', 'transaction-index')->name('transaction.index');

    Volt::route('pricelists', 'pricelist-index')->name('pricelist.index');
});


Route::prefix('b2b')->middleware(['auth', 'verified', 'customer'])->group(function () {
    Volt::route('cart', 'cart')->name('cart');
    Volt::route('history', 'history')->name('history');
});

Route::prefix('b2b')->middleware(['auth', 'verified', 'admin'])->group(function () {
    Volt::route('products/add', 'product-create')->name('product.create');
    Volt::route('products/{slug}/edit', 'product-create')->name('product.edit');

    Volt::route('setting', 'setting-index')->name('setting.index');

    Volt::route('categories/add', 'category-create')->name('category.create');
    Volt::route('categories/{slug}/edit', 'category-create')->name('category.edit');

    Volt::route('newsletters', 'newsletter-index')->name('newsletter.index');
    Volt::route('newsletters/add', 'newsletter-create')->name('newsletter.create');
    Volt::route('newsletters/{slug}/edit', 'newsletter-create')->name('newsletter.edit');
});

Volt::route('outlets/{slug}', 'outlet-show')->name('outlet-show');

Route::get('/test-email', [App\Http\Controllers\MailController::class, 'sendTestEmail']);
Route::resource('/api/test', ApiTestController::class)->names('jurnal.tests');

require __DIR__ . '/auth.php';
