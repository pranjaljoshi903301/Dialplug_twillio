<?php


\DB::table('settings')->insert([
    [
        'code' => 'classified_enable_google_location',
        'type' => 'BOOLEAN',
        'category' => 'Classified',
        'label' => 'Enable Google Location',
        'value' => 'false',
        'editable' => 1,
        'hidden' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]
]);


\Schema::table('classified_products', function (\Illuminate\Database\Schema\Blueprint $table) {

    $table->string('lat')->nullable()->after('location_id');
    $table->string('zip_code')->nullable()->after('location_id');;
    $table->string('long')->nullable()->after('location_id');;
    $table->string('address')->nullable()->after('location_id');;

});

