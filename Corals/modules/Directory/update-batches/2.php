<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

if (Schema::hasColumn('directory_listings_claim', 'brief_desctiption')) {
    Schema::table('directory_listings_claim', function (Blueprint $table) {
        $table->renameColumn('brief_desctiption', 'brief_description');
    });
}
