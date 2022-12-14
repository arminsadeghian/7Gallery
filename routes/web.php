<?php

use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\PaymentsController;
use App\Http\Controllers\Admin\ProductsController as AdminProductsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Home\CartController;
use App\Http\Controllers\Home\CheckoutController;
use App\Http\Controllers\Home\ProductsController as HomeProductsController;
use App\Http\Controllers\Payment\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('')->group(function () {
    Route::get('', [HomeProductsController::class, 'index'])->name('home.products.all');
    Route::get('{product_id}/show', [HomeProductsController::class, 'show'])->name('home.products.show');
    Route::get('{product_id}/addToCart', [CartController::class, 'addToCart'])->name('home.cart.addToCart');
    Route::get('{product_id}/removeFromCart', [CartController::class, 'removeFromCart'])->name('home.cart.removeFromCart');
    Route::get('checkout/cart', [CheckoutController::class, 'show'])->name('home.checkout.show');
});

Route::prefix('admin/')->middleware('auth')->group(function () {

    Route::get('dashboard', function () {
        return view('admin.dashboard.all');
    });

    Route::prefix('categories/')->group(function () {
        Route::get('all', [CategoriesController::class, 'all'])->name('admin.categories.all');
        Route::get('create', [CategoriesController::class, 'create'])->name('admin.categories.create');
        Route::post('', [CategoriesController::class, 'store'])->name('admin.categories.store');
        Route::delete('{category_id}/delete', [CategoriesController::class, 'delete'])->name('admin.categories.delete');
        Route::get('{category_id}/edit', [CategoriesController::class, 'edit'])->name('admin.categories.edit');
        Route::put('{category_id}/update', [CategoriesController::class, 'update'])->name('admin.categories.update');
    });

    Route::prefix('products/')->group(function () {
        Route::get('all', [AdminProductsController::class, 'all'])->name('admin.products.all');
        Route::get('create', [AdminProductsController::class, 'create'])->name('admin.products.create');
        Route::post('', [AdminProductsController::class, 'store'])->name('admin.products.store');
        Route::get('{product_id}/download/demo', [AdminProductsController::class, 'downloadDemo'])->name('admin.products.download.demo');
        Route::get('{product_id}/download/source', [AdminProductsController::class, 'downloadSource'])->name('admin.products.download.source');
        Route::delete('{product_id}/delete', [AdminProductsController::class, 'delete'])->name('admin.products.delete');
        Route::get('{product_id}/edit', [AdminProductsController::class, 'edit'])->name('admin.products.edit');
        Route::put('{product_id}/update', [AdminProductsController::class, 'update'])->name('admin.products.update');
    });

    Route::prefix('users/')->group(function () {
        Route::get('all', [UsersController::class, 'all'])->name('admin.users.all');
        Route::get('create', [UsersController::class, 'create'])->name('admin.users.create');
        Route::post('', [UsersController::class, 'store'])->name('admin.users.store');
        Route::delete('{user_id}/delete', [UsersController::class, 'delete'])->name('admin.users.delete');
        Route::get('{user_id}/edit', [UsersController::class, 'edit'])->name('admin.users.edit');
        Route::put('{user_id}/update', [UsersController::class, 'update'])->name('admin.users.update');
    });

    Route::prefix('orders/')->group(function () {
        Route::get('all', [OrdersController::class, 'all'])->name('admin.orders.all');
    });

    Route::prefix('payments/')->group(function () {
        Route::get('all', [PaymentsController::class, 'all'])->name('admin.payments.all');
    });

});

Route::prefix('payment')->group(function () {
    Route::post('pay', [PaymentController::class, 'pay'])->name('payment.pay');
    Route::post('callback', [PaymentController::class, 'callback'])->name('payment.callback');
});


// Auth Routes
Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');
Route::get('/login', [LoginController::class, 'loginView'])->name('login.view');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/register', [RegisterController::class, 'register'])->name('register');
