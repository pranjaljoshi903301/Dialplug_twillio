<?php


\Schema::table('ecommerce_orders', function (\Illuminate\Database\Schema\Blueprint $table) {

    $table->unsignedInteger('user_id')->nullable()->change();

});
