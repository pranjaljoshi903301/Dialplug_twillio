<?php


\Schema::table('classified_products', function (\Illuminate\Database\Schema\Blueprint $table) {

    $table->integer('visitors_count')->default(0)->after('user_id');

});