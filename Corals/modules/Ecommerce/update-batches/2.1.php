<?php

if (!\Schema::hasTable('ecommerce_category_attributes')) {
    Schema::create('ecommerce_category_attributes', function (\Illuminate\Database\Schema\Blueprint $table) {
        $table->increments('id');

        $table->unsignedInteger('attribute_id')->index();
        $table->unsignedInteger('category_id')->index();

        $table->foreign('attribute_id')
            ->references('id')->on('ecommerce_attributes')
            ->onUpdate('cascade')->onDelete('cascade');

        $table->foreign('category_id')->references('id')
            ->on('ecommerce_categories')
            ->onUpdate('cascade')->onDelete('cascade');
    });
}