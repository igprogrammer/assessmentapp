<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentFee extends Model
{
    use HasFactory;

    public static function getPaymentItems($paymentId){
        return PaymentFee::where('payment_id','=',$paymentId)->get();
    }

    public static function savePaymentItems($payment_id,$fee_item_id,$temp_payment_id,$fee_amount,$account_code,$fname,$fyear2,$fyear){

        $data = PaymentFee::where(['fee_item_id'=>$fee_item_id,'payment_id'=>$payment_id])->first();
        if (empty($data)){
            $payment_fee = new PaymentFee();
            $payment_fee->user_id = Auth::user()->id;
            $payment_fee->payment_id = $payment_id;
            $payment_fee->fee_item_id = $fee_item_id;
            $payment_fee->temp_payment_id = $temp_payment_id;
            $payment_fee->fee_amount = $fee_amount;
            $payment_fee->date_of_payment = date('d/m/Y',strtotime(date('Y-m-d')));
            $payment_fee->account_code = $account_code;
            $payment_fee->month = date('m');
            $payment_fee->year = date('Y');
            $payment_fee->fname = $fname;
            $payment_fee->fyear2 = $fyear2;
            $payment_fee->fyear = $fyear;
            $payment_fee->save();
        }else{
            $payment_fee = $data;
        }

        return $payment_fee;
    }

    public static function getAssessmentItems($paymentId){
        return DB::connection('sqlsrv')->table('payments as p')
            ->join('payment_fees as pf','pf.payment_id','=','p.id')
            ->join('fee_items as fi','fi.id','pf.fee_item_id')
            ->join('fees as f','f.id','=','fi.fee_id')
            ->where(['payment_id'=>$paymentId])->paginate();
    }
}
