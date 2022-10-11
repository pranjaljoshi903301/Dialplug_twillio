<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => ''], function () {
    Route::resource('bt_config', 'BitrixTelephonyController');
});

Route::group(['prefix' => ''], function () {
    Route::resource('bt_users', 'BTUsersController');
});

Route::get('bt_users/{id}/createUser', 'BTUsersController@create');
Route::get('bt_config/{id}/toggleSetupStatus', 'BitrixTelephonyController@toggleSetupStatus');
Route::get('bt_config/{id}/sendPasswordMail', 'BitrixTelephonyController@sendPasswordMail');
Route::get('bt_users/{id}/addPassword', 'BTUsersController@addPassword');
Route::post('bt_users/{id}/storePassword', 'BTUsersController@storePassword');
Route::get('bt_users/{id}/editSoftphonePassword', 'BTUsersController@editSoftphonePassword');
Route::post('bt_users/{id}/updateSoftphonePassword', 'BTUsersController@updateSoftphonePassword');
Route::get('bt_users/{id}/toggleSyncStatus', 'BTUsersController@toggleSyncStatus');
Route::get('bt_users/{id}/sendPasswordMail', 'BTUsersController@sendPasswordMail');

