<?php

namespace App\Models\Billing;

use App\Models\Booking\Booking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    protected $primaryKey = 'billId';

    protected $table = 'billings';


    public static function updateControlNumber($billId,$controlNumber){
        $bill = Billing::find($billId);

        if (!empty($bill)){
            $bill->payCtrNum = $controlNumber;
            $bill->save();

            return $bill;
        }

    }

    public static function getBill($reference){
        return Billing::where(['reference'=>$reference,'isSuccessfully'=>0])->first();
    }

    public static function updateBill($bookingId,$reference,$xml){
        $check = Billing::where(['bookingId'=>$bookingId])->first();
        if (!empty($check)){
            $bill = Billing::find($check->billId);
            //$bill->payCtrNum = $payCtrNum;
            $bill->reference = $reference;
            $bill->xmlContent = $xml;
            $bill->save();

            return $bill;
        }
    }

    public static function saveBill($billAmount,$currency,$entityNo,$bookingId,$reference){

        $check = Billing::where(['bookingId'=>$bookingId])->first();
        if (empty($check)){
            $billing = new Billing();
            $billing->billAmount = $billAmount;
            $billing->currency = $currency;
            $billing->month = date('m',strtotime(date('Y-m-d')));
            $billing->year = date('Y',strtotime(date('Y-m-d')));
            $billing->entityNo = $entityNo;
            $billing->bookingId = $bookingId;
            $billing->reference = $reference;
            $billing->save();
        }else{
            $billing = $check;
        }


        return $billing;
    }
}
