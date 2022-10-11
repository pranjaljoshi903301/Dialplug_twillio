<?php

Route::group(['prefix' => 'e-commerce', 'as' => 'api.e-commerce.'], function () {
    Route::apiResource('attributes', 'AttributesController', ['except' => 'show']);
    Route::apiResource('categories', 'CategoriesController', ['except' => 'show']);
    Route::apiResource('brands', 'BrandsController', ['except' => 'show']);
    Route::apiResource('coupons', 'CouponsController', ['except' => 'show']);
    Route::apiResource('tags', 'TagsController', ['except' => 'show']);
    Route::apiResource('shippings', 'ShippingsController', ['except' => 'show']);

    Route::group(['prefix' => 'shop', 'as' => 'shop.'], function () {
        Route::get('single-product/{product}', 'ShopController@singleProduct')->name('single-product');
        Route::get('products-list', 'ShopController@productsList')->name('products-list');
        Route::get('settings', 'ShopController@settings')->name('settings');
    });

    Route::group(['prefix' => 'checkout-public', 'as' => 'checkout-public.'], function () {
        Route::get('get-coupon-by-code/{code}', 'CheckoutPublicController@getCouponByCode')->name('get-coupon-by-code');
        Route::get('get-shipping-roles/{country_code}', 'CheckoutPublicController@getAvailableShippingRoles')->name('get-shipping-roles');
    });

    Route::group(['prefix' => 'checkout', 'as' => 'checkout.'], function () {
        Route::get('get-coupon-by-code/{code}', 'CheckoutController@getCouponByCode')->name('get-coupon-by-code');
        Route::get('get-shipping-roles/{country_code}', 'CheckoutController@getAvailableShippingRoles')->name('get-shipping-roles');
        Route::post('order-submit', 'CheckoutController@orderSubmit')->name('order-submit');
    });

    Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
        Route::get('my-orders', 'OrdersController@myOrders')->name('my-orders');
    });

    Route::apiResource('orders', 'OrdersController', ['only' => ['show']]);
});