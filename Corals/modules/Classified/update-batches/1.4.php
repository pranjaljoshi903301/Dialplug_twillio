<?php

\DB::table('settings')->insert([
    [
        'code' => 'classified_subscription_is_enable',
        'type' => 'BOOLEAN',
        'category' => 'Classified',
        'label' => 'Enable Classified Subscription Integration',
        'value' => 'false',
        'editable' => 1,
        'hidden' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'code' => 'classified_year_model_visible',
        'type' => 'BOOLEAN',
        'category' => 'Classified',
        'label' => 'Year Model field visible',
        'value' => 'false',
        'editable' => 1,
        'hidden' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'code' => 'classified_subscription_product_id',
        'type' => 'NUMBER',
        'category' => 'Classified',
        'label' => 'Subscription Product Id',
        'value' => '',
        'editable' => 1,
        'hidden' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'code' => 'classified_allowed_products_count_feature_id',
        'type' => 'NUMBER',
        'category' => 'Classified',
        'label' => 'Allowed Products Count Feature Id',
        'value' => '',
        'editable' => 1,
        'hidden' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'code' => 'classified_allowed_featured_products_count_feature_id',
        'type' => 'NUMBER',
        'category' => 'Classified',
        'label' => 'Allowed Featured Products Count Feature Id',
        'value' => '',
        'editable' => 1,
        'hidden' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ],
]);

\Schema::table('classified_products', function (\Illuminate\Database\Schema\Blueprint $table) {
    $table->integer('year_model')->nullable()->after('price_on_call');
});