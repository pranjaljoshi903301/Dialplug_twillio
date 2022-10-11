<?php

Route::get('exotel-install', 'BarsController@exotel_install');
Route::post('exotel-install', 'BarsController@exotel_install');

/* User Interface Methods  [9] */
Route::get('exotel-sync', 'BarsController@exotel_view_index');
Route::post('exotel-sync', 'BarsController@exotel_view_index');
Route::post('exotel/store-details', 'BarsController@store_details');


/* Click to Call Methods  [9] */
Route::post('exotel/click-to-call', 'BarsController@click_to_call');
Route::post('exotel/callback-url', 'BarsController@callback_url');
Route::get('exotel/incoming-click-to-call', 'BarsController@incoming_click_to_call');

/* Debug Method */
Route::get('exotel/test', 'BarsController@testing');
Route::post('exotel/index.html', 'BarsController@app_redirect');