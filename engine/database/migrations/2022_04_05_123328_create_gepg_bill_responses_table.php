<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGepgBillResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gepg_bill_responses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('billId')->nullable();
            $table->text('response_content')->nullable();
            $table->dateTime('posted_on')->nullable();
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
        Schema::dropIfExists('gepg_bill_responses');
    }
}
