<?php

Route::group(['prefix' => ''], function () {
    Route::get('import-demo-data', 'DemosController@importDummyData');
});