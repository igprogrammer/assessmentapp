<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeeItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fee_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('fee_id')->unsigned()->nullable();
            $table->string('item_name')->nullable();
            $table->string('item_amount')->nullable();
            $table->string('penalty_amount')->nullable();
            $table->string('days')->nullable();
            $table->string('copy_charge')->nullable();
            $table->string('stamp_duty_amount')->nullable();
            $table->string('currency')->nullable();
            $table->string('anniversary')->default('no');
            $table->string('active')->default('yes');
            $table->timestamps();


            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');
            $table->foreign('fee_id')->references('id')->on('fees')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fee_items');
    }
}
