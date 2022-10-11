<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => ''], function () {
    Route::resource('bars', 'BarsController');
    Route::resource('twillio-dashboard', 'BarsController');
});