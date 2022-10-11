<?php

Route::group(['prefix' => 'classified'], function () {
    // admin routes
    Route::post('products/bulk-action', 'ProductsController@bulkAction');
    Route::resource('products', 'ProductsController', ['except' => ['show']]);

    Route::group(['prefix' => 'wishlist'], function () {
        Route::post('{product}', 'WishlistController@setWishlist');
        Route::get('my', ['as' => 'my-wishlist', 'uses' => 'WishlistController@myWishlist']);
    });


    //User routes
    Route::group([
        'prefix' => 'user',
        'middleware' => array_merge(['auth'],
            \Corals\Modules\Classified\Facades\Classified::subscriptionActive() ? [\Corals\Modules\Subscriptions\Middleware\SubscriptionMiddleware::class] : [])],
        function () {
            Route::get('dashboard', 'DashboardController@index');
            Route::post('{user}/rate', ['as' => 'show', 'uses' => 'RatingController@createRating']);
            Route::resource('products', 'UserProductsController');
        });
});

Route::group(['prefix' => 'products'], function () {

    Route::get('/', 'ProductsPublicController@index');
    Route::get('{slug}', 'ProductsPublicController@show');
    Route::post('{product}/report', ['as' => 'report_product', 'uses' => 'ProductsPublicController@report']);
    Route::post('{product}/refer', ['as' => 'refer_product', 'uses' => 'ProductsPublicController@refer']);
});


