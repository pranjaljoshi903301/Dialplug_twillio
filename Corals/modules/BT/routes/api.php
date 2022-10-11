<?php

use Corals\Modules\BT\Http\Controllers\API\TwilioCausebroadcastingController;

Route::group([], function () {
  // Route::post('users/{user}/address', 'UserAddressesController@store')->name('api.users.address.store');
  // Route::delete('users/{user}/address/{type}', 'UserAddressesController@destroy')->name('api.users.address.destroy');

  // Route::apiResource('bitrixtelephony', 'BitrixTelephonyController', ['as' => 'api.bitrixtelephony']);
  Route::get('bitrixtelephony', 'BitrixTelephonyController@index');
  Route::get('bitrixtelephony/{bt_config}', 'BitrixTelephonyController@get_by_url');
  Route::put('bitrixtelephony/{url}', 'BitrixTelephonyController@update_by_url');

  //  Temp
  Route::get('bitrix_auth', 'BitrixTelephonyController@bitrix_auth');
});

/* Exotel Route Moved to Exotel Module
 
  Route::post('exotel/click-to-call', 'BitrixTelephonyController@click_to_call');
 Route::post('exotel/callback-url', 'BitrixTelephonyController@callback_url');

 Route::get('exotel/incoming-click-to-call', 'BitrixTelephonyController@incoming_click_to_call');

 Route::post('exotel-sync', 'BitrixTelephonyController@exotel_view_index');

 Route::get('exotel/test', 'BitrixTelephonyController@testing');

 Route::post('exotel/store-details', 'BitrixTelephonyController@store_details');

*/ 

// call-by-mobile
Route::post('bt/mobile_validation', 'BitrixTelephonyController@mobile_validation');

// call-by-bitrix-twilio
Route::get('/causebroadcasting/status-check', 'TwilioCausebroadcastingController@status_check');
Route::post('/causebroadcasting/outgoing_call', 'TwilioCausebroadcastingController@outgoing_call');
Route::get('/causebroadcasting/outgoing_call', 'TwilioCausebroadcastingController@outgoing_call');
Route::post('/causebroadcasting/outgoing_callback', 'TwilioCausebroadcastingController@outgoing_callback');
Route::get('/causebroadcasting/outgoing_callback', 'TwilioCausebroadcastingController@outgoing_callback');

/* debug route */
Route::post('/twiliodev/outgoing_call', 'TwilioDevController@outgoing_call');
Route::get('/twiliodev/outgoing_call', 'TwilioDevController@outgoing_call');

Route::post('/twiliodev/outgoing_callback', 'TwilioDevController@outgoing_callback');
Route::get('/twiliodev/outgoing_callback', 'TwilioDevController@outgoing_callback');

/* twilio-bitrix development */
Route::post('/bitrix-twilio/outgoing_call', 'TwilioBitrixController@outgoing_call');
Route::post('/bitrix-twilio/outgoing_callback', 'TwilioBitrixController@outgoing_callback');
Route::post('/bitrix-twilio/extension-token-request', 'TwilioBitrixController@extension_token_request');
/* twilio-bitrix development */