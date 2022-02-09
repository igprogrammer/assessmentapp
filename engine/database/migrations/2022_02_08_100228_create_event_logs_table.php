<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_logs', function (Blueprint $table) {
            $table->bigIncrements('eventId');
            $table->string('username')->nullable();
            $table->string('eventCategory')->nullable();
            $table->string('eventLevel')->nullable();
            $table->string('fullName')->nullable();
            $table->string('eventStatus')->nullable();
            $table->string('action')->nullable();
            $table->text('description')->nullable();
            $table->string('ipAddress')->nullable();
            $table->string('macAddress')->nullable();
            $table->string('controllerName');
            $table->string('functionName');
            $table->text('exception')->nullable();
            $table->text('trace')->nullable();
            $table->string('lineNo')->nullable();
            $table->text('customMessage')->nullable();
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
        Schema::dropIfExists('event_logs');
    }
}
