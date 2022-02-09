<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGepgErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gepg_errors', function (Blueprint $table) {
            $table->increments('id');
			$table->string('BillId')->nullable();
            $table->string('PayCntrNum');
            $table->string('TrxSts');
			$table->string('TrxStsCode');
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
        Schema::dropIfExists('gepg_errors');
    }
}
