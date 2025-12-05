<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\ProductSkuController;
use App\Http\Controllers\AuthAuthenticatedSessionController;
use App\Http\Controllers\AdminNotificationController;


// // Frontend routes
// Route::get('/', [HomeController::class, 'index'])->name('home');
// Route::get('/shop', [HomeController::class, 'shop'])->name('shop');

// Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.detail');
// Route::get('/cart', [HomeController::class, 'cart'])->name('cart');
// Route::get('/checkout', [HomeController::class, 'checkout'])->name('checkout');
// Route::patch('/cart/update', [HomeController::class, 'updateCart'])->name('cart.update');
// Route::delete('/cart/remove', [HomeController::class, 'removeCart'])->name('cart.remove');

Route::get('/', function () {
    return redirect()->route('login');
});



// // User Dashboard
// Route::middleware('auth')->group(function () {
//     Route::get('/my-orders', [UserController::class, 'orders'])->name('user.orders');
//     Route::get('/my-orders/{id}', [UserController::class, 'orderDetail'])->name('user.orders.detail');
// });



//dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


//cart
Route::resource('carts', CartController::class);

Route::get('carts/{cart}/items', [CartItemController::class, 'index'])->name('carts.items');
Route::post('cart-items/store', [CartItemController::class, 'store'])->name('cartitems.store');
Route::get('cart-items/{id}/edit', [CartItemController::class, 'edit'])->name('cartitems.edit');
Route::delete('cart-items/{id}', [CartItemController::class, 'destroy'])->name('cartitems.delete');

//order
Route::resource('orders', OrderController::class);

//  Invoice PDF
Route::get('/orders/{id}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');


Route::resource('product-skus', ProductSkuController::class);

Route::get('products/{id}/skus', [ProductSkuController::class, 'productSkus'])->name('products.skus');

Route::get('/notifications', [App\Http\Controllers\AdminNotificationController::class, 'fetch'])
    ->name('notifications.fetch');


Route::post('/orders/change-status', [OrderController::class, 'changeStatus'])
    ->name('orders.change-status');

// Auth routes (login, register, forgot password, etc.)
Route::post('/logout', [AuthAuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Auth::routes();

// Home page after login
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Protected routes (only accessible when logged in)
Route::middleware(['auth'])->group(function () {

    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');


    //order
    Route::resource('orders', OrderController::class)->except(['edit', 'update']);

    
    Route::post('orders/change-status', [OrderController::class, 'changeStatus'])->name('orders.change-status');

    
    //order item
    Route::get('orders/{order}/items', [OrderItemController::class, 'index'])->name('order.items');
Route::post('order-items/store', [OrderItemController::class, 'store'])->name('orderitems.store');
Route::get('order-items/{id}/edit', [OrderItemController::class, 'edit'])->name('orderitems.edit');
Route::delete('order-items/{id}', [OrderItemController::class, 'destroy'])->name('orderitems.delete');
    

    // Product Variants
    Route::prefix('products/{product}')->group(function () {
        Route::resource('variants', ProductVariantController::class);
    });

    // Brands
    Route::resource('brands', BrandController::class);

    
    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories/store', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/edit/{id}', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::delete('/categories/delete/{id}', [CategoryController::class, 'destroy'])->name('categories.delete');

    // Coupon CRUD
Route::group(['middleware' => ['auth']], function() {
    Route::resource('coupons', App\Http\Controllers\CouponController::class);
    
    // Optional: AJAX toggle status
    Route::post('coupons/change-status', [App\Http\Controllers\CouponController::class, 'changeStatus'])->name('coupons.change-status');
});
});