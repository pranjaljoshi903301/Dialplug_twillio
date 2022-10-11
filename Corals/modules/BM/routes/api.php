<?php

Route::get('bm/mobile/{mobile_number}', 'BitrixMobileController@get_by_mobile_number');

// call-by-lambda
Route::post('bm/is_user_subscribed_or_in_trial', 'BitrixMobileController@is_user_subscribed_or_in_trial');

// call-by-mobile
Route::post('bm/mobile_validation', 'BitrixMobileController@mobile_validation');

Route::post('bm/test', 'BitrixMobileController@testing');
Route::get('plugin/test', 'PluginController@test');

Route::post('plugin/user_validation', 'PluginController@user_validation');
Route::post('plugin/unique_id_data', 'PluginController@unique_id_data');

/* cloud call api */
Route::post('bm/cloud_call','CloudCallController@cloud_call');