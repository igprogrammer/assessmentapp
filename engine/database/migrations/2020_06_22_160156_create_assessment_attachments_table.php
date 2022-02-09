<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssessmentAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('payment_id')->unsigned()->nullable();
            $table->string('file_path')->nullable();
            $table->string('mime')->nullable();
            $table->string('file_name')->nullable();
            $table->string('extension')->nullable();
            $table->timestamps();

            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assessment_attachments');
    }
}
