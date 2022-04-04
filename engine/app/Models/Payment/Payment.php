<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Payment extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public static function savePayment($customer_id,$temp_payment_id,$total_amount,$account_code,$currency,$company_number,$invoice,$re_assessment_description){

        $data = Payment::where(['temp_payment_id'=>$temp_payment_id])->first();
        if (empty($data)){
            $payment = new Payment();
            $payment->user_id = Auth::user()->id;
            $payment->customer_id =$customer_id;
            $payment->temp_payment_id = $temp_payment_id;
            $payment->amount = $total_amount;
            $payment->cheque_amount = '';
            $payment->payment_type = 'Cash';
            $payment->cheque_amount = '';
            $payment->date_of_payment = date('d/m/Y',strtotime(date('Y-m-d')));
            $payment->month = date('m');
            $payment->year = date('Y');
            $payment->account_code = $account_code;
            $payment->pay_type = 'none';
            $payment->currency = $currency;
            $payment->app_print = 'no';
            $payment->regno = $company_number;
            $payment->invoice = $invoice;
            $payment->reference = $invoice;
            $payment->re_assessment_description = $re_assessment_description;
            $payment->add_date = date('Y-m-d');
            $payment->save();
        }else{
            $payment = $data;
        }


        return $payment;
    }

    public static function getAssessmentRecords($flag, $fromDate = null, $toDate = null){
        $records = array();
        if ($fromDate != null && $toDate != null){
            $fromDate = date('Y-m-d 00:00:00', strtotime($fromDate));
            $toDate = date('Y-m-d 23:59:59', strtotime($toDate));
        }

        if (strtolower($flag) == 'individual'){

            $records = Payment::where('payments.user_id','=',Auth::user()->id);

            if ($fromDate != null && $toDate != null){
                $records = $records->whereBetween('created_at', array($fromDate,$toDate));
            }

            $records = $records->orderBy('payments.id','DESC')->get();

        }elseif (strtolower($flag) == 'all'){

            $records = Payment::orderBy('payments.id','DESC');

            if ($fromDate != null && $toDate != null){
                $records = $records->whereBetween('created_at', array($fromDate,$toDate));
            }

            $records = $records->get();

        }elseif (strtolower($flag) == 'tmp'){

            $records = TempPayment::where('temp_payments.user_id','=',Auth::user()->id);

            if ($fromDate != null && $toDate != null){
                $records = $records->whereBetween('created_at', array($fromDate,$toDate));
            }

            $records = $records->orderBy('temp_payments.id','DESC')->get();

        }

        return $records;

    }
}
