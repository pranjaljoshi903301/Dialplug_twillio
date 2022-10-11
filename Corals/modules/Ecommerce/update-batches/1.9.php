<?php


//Add Permission Brands
\DB::table('permissions')->insert([
    [
        'name' => 'Ecommerce::brand.view',
        'guard_name' => config('auth.defaults.guard'),
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now(),
    ],
    [
        'name' => 'Ecommerce::brand.create',
        'guard_name' => config('auth.defaults.guard'),
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now(),
    ],
    [
        'name' => 'Ecommerce::brand.update',
        'guard_name' => config('auth.defaults.guard'),
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now(),
    ],
    [
        'name' => 'Ecommerce::brand.delete',
        'guard_name' => config('auth.defaults.guard'),
        'created_at' => \Carbon\Carbon::now(),
        'updated_at' => \Carbon\Carbon::now(),
    ],
]);
