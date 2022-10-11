<?php


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

$tables = [
    'amazon_imports',
    'amazon_categories',
];

foreach ($tables as $tableName) {
    if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'properties')) {
        Schema::table($tableName, function (Blueprint $table) {
            $table->text('properties')->nullable();
        });
    }
}
