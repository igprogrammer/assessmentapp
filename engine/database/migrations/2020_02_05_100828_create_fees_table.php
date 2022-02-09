<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('fee_account_id')->unsigned()->nullable();
            $table->string('fee_name');
            $table->string('fee_code');
            $table->string('account_code');
            $table->string('amount');
            $table->string('currency')->default('TSHs');
            $table->string('has_form')->default('no');
            $table->string('type')->default('new');
            $table->string('gfs_code');
            $table->string('active')->default('no');
            $table->timestamps();


            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');
            $table->foreign('fee_account_id')->references('id')->on('fee_accounts')->onDelete('no action');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fees');
    }
}
