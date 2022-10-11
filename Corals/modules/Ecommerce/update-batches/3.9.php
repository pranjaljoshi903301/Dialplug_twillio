<?php


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

$tables = [
    'ecommerce_coupons',
    'ecommerce_shippings',
    'ecommerce_tags',
    'ecommerce_attributes',
    'ecommerce_categories',
    'ecommerce_sku',
    'ecommerce_brands',
    'ecommerce_products',
];

foreach ($tables as $tableName) {
    if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'properties')) {
        Schema::table($tableName, function (Blueprint $table) {
            $table->text('properties')->nullable();
        });
    }
}
