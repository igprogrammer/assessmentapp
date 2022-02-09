<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('temp_payment_id')->unsigned()->nullable();
            $table->integer('customer_id')->unsigned()->nullable();
            $table->string('amount')->nullable();
            $table->string('cheque_amount')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('cheque_no')->nullable();
            $table->string('date_of_payment')->nullable();
            $table->string('month')->nullable();
            $table->string('year')->nullable();
            $table->string('account_code')->nullable();
            $table->string('pay_type')->default('none');
            $table->string('currency')->default('TSHs');
            $table->string('app_print')->default('no');
            $table->string('register')->nullable();
            $table->string('regno')->nullable();
            $table->string('reg_date')->nullable();
            $table->string('invoice')->nullable();
            $table->string('status')->nullable();
            $table->string('re_assessment_description')->nullable();
            $table->string('reference')->nullable();
            $table->date('add_date')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('no action');
            $table->foreign('temp_payment_id')->references('id')->on('temp_payments')->onDelete('no action');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
