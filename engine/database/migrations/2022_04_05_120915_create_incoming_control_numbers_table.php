<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomingControlNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incoming_control_numbers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('xmlContent')->nullable();
            $table->bigInteger('billId')->nullable();
            $table->dateTime('postedOn')->nullable();
            $table->string('receiveMessage')->nullable();
            $table->string('responseMessage')->nullable();
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('incoming_control_numbers');
    }
}
