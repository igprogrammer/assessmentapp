<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGepgCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gepg_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('BillId')->nullable();
            $table->string('PayCntrNum')->nullable();
            $table->string('TrxSts')->nullable();
            $table->string('TrxStsCode')->nullable();
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
        Schema::dropIfExists('gepg_codes');
    }
}
