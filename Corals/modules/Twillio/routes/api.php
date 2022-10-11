<?php

Route::get('twillio-install', 'BarsController@twillio_install');
Route::post('twillio-install', 'BarsController@twillio_install');

/* User Interface Methods  [9] */
Route::get('twillio-sync', 'BarsController@twillio_view_index');
Route::post('twillio-sync', 'BarsController@twillio_view_index');
Route::post('twillio/store-details', 'BarsController@store_details');


/* Click to Call Methods  [9] */
Route::post('twillio/click-to-call', 'BarsController@click_to_call');
Route::post('twillio/callback-url', 'BarsController@callback_url');
Route::get('twillio/incoming-click-to-call', 'BarsController@incoming_click_to_call');

/* Debug Method */
Route::get('twillio/test', 'BarsController@testing');
Route::post('twillio/index.html', 'BarsController@app_redirect');