<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->bigIncrements('billId');
            $table->string('billAmount')->nullable();
            $table->string('currency')->nullable();
            $table->string('month')->nullable();
            $table->year('year')->nullable();
            $table->text('xmlContent')->nullable();
            $table->integer('entityNo')->nullable();
            $table->integer('bookingId')->nullable();
            $table->string('payCtrNum')->nullable();
            $table->string('reference')->nullable();
            $table->boolean('isSuccessfully')->default(0);
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
        Schema::dropIfExists('billings');
    }
}
