<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => ''], function () {
    Route::resource('bm_config', 'BitrixMobileController');
});

Route::get('bm_config/{id}/manage_details', 'BitrixMobileController@manage_details');
Route::post('bm_config/{id}/update_details', 'BitrixMobileController@update_details');
Route::get('bm_config/{id}/delete_details', 'BitrixMobileController@delete_details');