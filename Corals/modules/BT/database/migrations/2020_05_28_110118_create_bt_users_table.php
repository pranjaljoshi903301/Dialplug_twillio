<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBTUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bt_users', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Dialplug Users Fields
            $table->string('user_id');
            $table->string('bitrix_user_id');
            $table->string('bitrix_user_name');
            $table->string('is_default');
            $table->String('inbound_route');
	    $table->String('company_name');
            // Dialplug Users Fields End

            $table->unsignedInteger('created_by')->nullable()->index();
            $table->unsignedInteger('updated_by')->nullable()->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bt_users');
    }
}
