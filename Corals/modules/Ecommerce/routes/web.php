<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'e-commerce', 'as' => 'e-commerce.'], function () {
    Route::get('shop', ['as' => 'index', 'uses' => 'ShopController@index']);
    Route::get('shop/{slug}', ['as' => 'ec-show', 'uses' => 'ShopController@show']);
    Route::get('cart', 'CartController@index');

    Route::group(['prefix' => 'checkout'], function () {
        Route::get('/', 'CheckoutController@index');
        Route::post('/', 'CheckoutController@doCheckout');
        Route::get('step/{step}', 'CheckoutController@checkoutStep');
        Route::post('step/{step}', 'CheckoutController@saveCheckoutStep');
        Route::get('redirect/{gateway}/{order}', 'CheckoutController@redirectPage')
            ->middleware('signed')
            ->name('ecommerce.checkout.redirect');

        Route::get('shipping-address', 'CheckoutController@checkoutShippingAddress');
        Route::get('order-success/{order}', 'CheckoutController@showOrderSuccessPage');
    });

    Route::get('products/download/{id}', 'ProductsController@downloadFile');
    Route::post('products/bulk-action', 'ProductsController@bulkAction');
    Route::post('categories/bulk-action', 'CategoriesController@bulkAction');
    Route::post('tags/bulk-action', 'TagsController@bulkAction');
    Route::post('brands/bulk-action', 'BrandsController@bulkAction');
    Route::resource('products', 'ProductsController');
    Route::resource('categories', 'CategoriesController', ['except' => ['show']]);
    Route::resource('attributes', 'AttributesController', ['except' => ['show']]);
    Route::resource('tags', 'TagsController', ['except' => ['show']]);
    Route::resource('brands', 'BrandsController', ['except' => ['show']]);
    Route::post('products/{product}/create-gateway-product', ['as' => 'create-gateway-product', 'uses' => 'ProductsController@createGatewayProduct']);

    Route::group(['prefix' => 'wishlist'], function () {
        Route::post('{product}', 'WishlistController@setWishlist');
        Route::delete('{wishlist}', 'WishlistController@destroy');
        Route::get('my', ['as' => 'my-wishlist', 'uses' => 'WishlistController@myWishlist']);

    });

    Route::get('products/sku/{sku}', 'SKUController@getSKU');
    Route::resource('products.sku', 'SKUController');
    Route::post('products/{product}/sku/{sku}/create-gateway-sku', ['as' => 'create-gateway-sku', 'uses' => 'SKUController@createGatewaySKU']);

    Route::get('downloads/my', ['as' => 'my-downloads', 'uses' => 'OrdersController@myDownloads']);
    Route::get('private-pages/my', ['as' => 'my-private-pages', 'uses' => 'OrdersController@myPrivatePages']);

    Route::group(['prefix' => 'orders'], function () {
        Route::post('/bulk-action', 'OrdersController@bulkAction');
        Route::get('my', ['as' => 'my-orders', 'uses' => 'OrdersController@myOrders']);
        Route::get('{order}/edit-payment', 'OrdersController@editPaymentDetails');
        Route::get('{order}/edit-status', 'OrdersController@editOrderStatus');

        Route::get('{order}/edit-shipping', 'OrdersController@editShippingDetails');
        Route::put('{order}/payment', 'OrdersController@updatePaymentDetails');
        Route::put('{order}/shipping', 'OrdersController@updateShippingDetails');
        Route::put('{order}/status', 'OrdersController@updateOrderStatus');
        Route::post('{order}/notify-buyer', 'OrdersController@notifyBuyer');
        Route::get('{order}/refund-order', 'OrdersController@getRefundView');
        Route::put('{order}/do-refund', 'OrdersController@doRefund');
        Route::get('{order}/track', 'OrdersController@track');
        Route::get('{order}/download/{id}', 'OrdersController@downloadFile');
        Route::post('calculate', 'OrdersController@calculateOrder');
    });

    Route::resource('orders', 'OrdersController', ['except' => ['destroy']]);

    Route::get('settings', 'ShopController@settings');
    Route::post('settings', 'ShopController@saveSettings');


    Route::post('coupons/bulk-action', 'CouponsController@bulkAction');
    Route::resource('coupons', 'CouponsController');
    Route::resource('shippings', 'ShippingsController');
});


Route::group(['prefix' => 'shop', 'as' => 'shop.'], function () {
    Route::get('/', ['as' => 'index', 'uses' => 'ShopPublicController@index']);
    Route::post('{product}/rate', ['as' => 'show', 'uses' => 'RatingController@createRating']);
    Route::get('{slug}', ['as' => 'ec-show', 'uses' => 'ShopPublicController@show']);
});


Route::group(['prefix' => 'cart'], function () {
    Route::get('/', 'CartPublicController@index');
    Route::get('/summary', 'CartPublicController@getCartItemsSummary');
    Route::post('empty', 'CartPublicController@emptyCart');
    Route::post('quantity/{itemhash}', 'CartPublicController@setQuantity');
    Route::post('{product}/add-to-cart/{sku?}', 'CartPublicController@addToCart');
});

Route::group(['prefix' => 'checkout'], function () {
    Route::get('gateway-payment/{gateway}/{order?}', 'CheckoutPublicController@gatewayPayment');
    Route::get('gateway-payment-token/{gateway}/{order?}', 'CheckoutPublicController@gatewayPaymentToken');
    Route::get('gateway-check-payment-token/{gateway}', 'CheckoutPublicController@gatewayCheckPaymentToken');


    Route::get('/', 'CheckoutPublicController@index');
    Route::post('/', 'CheckoutPublicController@doCheckout');
    Route::get('step/{step}', 'CheckoutPublicController@checkoutStep');
    Route::post('step/{step}', 'CheckoutPublicController@saveCheckoutStep');
    Route::get('redirect/{gateway}/{order}', 'CheckoutPublicController@redirectPage')
        ->middleware('signed')
        ->name('ecommerce.public.checkout.redirect');
    Route::get('shipping-address', 'CheckoutPublicController@checkoutShippingAddress');
    Route::get('order-success/{order}', 'CheckoutPublicController@showOrderSuccessPage');
});

