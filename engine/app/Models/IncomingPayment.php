<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class IncomingPayment extends Model
{
    use HasFactory;

    public static function getPendingIncomingPayments(){
        return DB::table('incoming_payments')->where(['status'=>0])->get();
    }

    public static function saveIncomingPayment($bookingId,$xmlContent,$TrxId,$SpCode,$PayRefId,$PayCtrNum,$BillAmt,$PaidAmt,
                                               $BillPayOpt,$CCy,$TrxDtTm,$UsdPayChnl,$PyrCellNum,$PyrEmail,$PyrName,
                                               $PspReceiptNumber,$PspName,$message){
        $data = new IncomingPayment();
        $data->xmlContent = $xmlContent;
        $data->billId = $bookingId;
        $data->postedOn = Carbon::now('Africa/Dar_es_Salaam');
        $data->TrxId = $TrxId;
        $data->SpCode =$SpCode;
        $data->PayRefId =$PayRefId;
        $data->PayCtrNum = $PayCtrNum;
        $data->BillAmt = $BillAmt;
        $data->PaidAmt = $PaidAmt;
        $data->BillPayOpt = $BillPayOpt;
        $data->CCy = $CCy;
        $data->TrxDtTm = $TrxDtTm;
        $data->UsdPayChnl = $UsdPayChnl;
        $data->PyrCellNum = $PyrCellNum;
        $data->PyrEmail = $PyrEmail;
        $data->PyrName = $PyrName;
        $data->PspReceiptNumber = $PspReceiptNumber;
        $data->PspName = $PspName;
        $data->message = $message;
        $data->save();

    }

    public static function getIncomingPaymentInfo($billId){

        return DB::table('incoming_payments')->where(['billId'=>$billId])->first();

    }
}
