<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErrorsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('errors_logs', function (Blueprint $table) {
            $table->bigIncrements('errorId');
            $table->string('ControllerName')->nullable();
            $table->string('FunctionName')->nullable();
            $table->text('Exception')->nullable();
            $table->text('Trace')->nullable();
            $table->text('ErrorMessage')->nullable();
            $table->text('CustomMessage')->nullable();
            $table->string('Line')->nullable();
            $table->string('name');
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
        Schema::dropIfExists('errors_logs');
    }
}
