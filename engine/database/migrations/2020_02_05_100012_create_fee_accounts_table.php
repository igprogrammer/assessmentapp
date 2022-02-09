<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeeAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fee_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('division_id')->unsigned()->nullable();
            $table->string('account_code');
            $table->string('account_name');
            $table->string('group_number');
            $table->timestamps();


            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');
            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fee_accounts');
    }
}
