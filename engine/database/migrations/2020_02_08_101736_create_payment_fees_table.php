<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_fees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('payment_id')->unsigned()->nullable();
            $table->integer('fee_item_id')->unsigned()->nullable();
            $table->integer('temp_payment_id')->unsigned()->nullable();
            $table->string('fee_amount')->nullable();
            $table->string('date_of_payment');
            $table->string('account_code');
            $table->string('month');
            $table->string('year');
            $table->string('fname')->nullable();
            $table->string('fyear2')->nullable();
            $table->string('fyear')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');
            $table->foreign('fee_item_id')->references('id')->on('fee_items')->onDelete('no action');
            $table->foreign('temp_payment_id')->references('id')->on('temp_payments')->onDelete('no action');
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_fees');
    }
}
