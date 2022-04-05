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
            $table->text('xml_content')->nullable();
            $table->bigInteger('billId')->nullable();
            $table->dateTime('posted_on')->nullable();
            $table->string('receive_message')->nullable();
            $table->string('response_message')->nullable();
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
