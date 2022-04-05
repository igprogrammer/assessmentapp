<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncomingPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incoming_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('xml_content')->nullable();
            $table->bigInteger('billId')->nullable();
            $table->dateTime('posted_on')->nullable();
            $table->string('TrxId')->nullable();
            $table->string('SpCode')->nullable();
            $table->string('PayRefId')->nullable();
            $table->string('PayCtrNum')->nullable();
            $table->string('BillAmt')->nullable();
            $table->string('PaidAmt')->nullable();
            $table->integer('BillPayOpt')->nullable();
            $table->string('CCy')->nullable();
            $table->string('TrxDtTm')->nullable();
            $table->string('UsdPayChnl')->nullable();
            $table->string('PyrCellNum')->nullable();
            $table->string('PyrEmail')->nullable();
            $table->string('PyrName')->nullable();
            $table->string('PspReceiptNumber')->nullable();
            $table->string('PspName')->nullable();
            $table->string('message')->nullable();
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
        Schema::dropIfExists('incoming_payments');
    }
}
