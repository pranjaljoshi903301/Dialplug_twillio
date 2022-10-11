<?php

\Schema::table('ecommerce_orders', function (\Illuminate\Database\Schema\Blueprint $table) {
    $table->text('properties')->nullable()->after('user_id');
});

\Schema::table('ecommerce_order_items', function (\Illuminate\Database\Schema\Blueprint $table) {
    $table->text('properties')->nullable()->after('order_id');
});

\Schema::table('ecommerce_order_items', function (\Illuminate\Database\Schema\Blueprint $table) {
    $table->string('tax_ids')->nullable()->after('quantity');
});

\DB::table('permissions')->updateOrInsert(['name' => 'Ecommerce::order.create'], [
    'guard_name' => config('auth.defaults.guard'),
    'created_at' => \Carbon\Carbon::now(),
    'updated_at' => \Carbon\Carbon::now(),
]);

\DB::table('permissions')->updateOrInsert(['name' => 'Ecommerce::order.delete'], [
    'guard_name' => config('auth.defaults.guard'),
    'created_at' => \Carbon\Carbon::now(),
    'updated_at' => \Carbon\Carbon::now(),
]);
