<?php

namespace App\Models\Payment;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public static function updateBillPrinting($paymentId,$isBillPrinted){
        $payment = Payment::find($paymentId);
        if (!empty($payment)){
            $payment->isBillPrinted = $isBillPrinted;
            $payment->save();
            return $payment;
        }
    }

    public static function setAccountantId($paymentId,$isReceiptPrinted){
        $payment = Payment::find($paymentId);
        if (!empty($payment)){
            $payment->accountantId = Auth::user()->id;
            $payment->printedDate = Carbon::now('Africa/Dar_es_Salaam');
            $payment->isReceiptPrinted = $isReceiptPrinted;
            $payment->save();
            return $payment;
        }
    }

    public static function updatePaymentReceiptNumber($bookingId,$receiptNumber){
        DB::table('payments')->where(['bookingId'=>$bookingId])->update(array(
            'receiptNo'=>$receiptNumber
        ));
    }

    public static function markPaymentAsReceived($bookingId,$amount,$payDate,$bankName,$phoneNumber,
                                                 $bankTransactionId,$mobileTransactionId,$paymentMethod,$PayRefId,$TrxStsCode,$PspReceiptNumber,$payMonth,$payYear,$spCode){

        DB::table('payments')->where(['bookingId'=>$bookingId])->update(array(
            'isPaid'=>1,
            'payDate'=>$payDate,
            'bankName'=>$bankName,
            'payPhoneNumber'=>$phoneNumber,
            'paidAmount' => $amount,
            'bank_transaction_id'=>$bankTransactionId,
            'mobile_transaction_id'=>$mobileTransactionId,
            'bank_account_number'=>$bankName,
            'method'=>$paymentMethod,
            'payrefid'=>$PayRefId,
            'PspReceiptNumber'=>$PspReceiptNumber,
            'gepgStatus'=>$TrxStsCode,
            'payMonth'=>$payMonth,
            'payYear'=>$payYear,
            'spCode'=>$spCode,
            'paymentEntryDate'=>Carbon::now('Africa/Dar_es_Salaam')));

    }

    public static function checkPaymentCompleted($bookingId){
        return DB::table('payments')->where(['bookingId'=>$bookingId,'isPaid'=>1])->first();
    }

    public static function updateControlNumber($bookingId,$controlNumber){

        $check = Payment::getPaymentInfoByBookingId($bookingId);
        if (!empty($check)){
            $pay = Payment::find($check->id);
            $pay->controlNumber = $controlNumber;
            $pay->invoice = $controlNumber;
            $pay->save();
        }else{
            $pay = $check;
        }

        return $pay;

        /*DB::table('payments')->where(['bookingId'=>$bookingId])->update(array(
            'controlNumber'=>$controlNumber,
            'invoice'=>$controlNumber
        ));*/
    }

    public static function getPaymentInfoByReference($reference){
        return  DB::table('payments')->where(['reference'=>$reference])->first();

    }

    public static function getPaymentInfoByBookingId($bookingId){
        return  DB::table('payments')->where(['bookingId'=>$bookingId])->first();
    }

    public static function updatePayment($paymentId,$total_amount,$invoice,$curr,$sc,$comma_separated,$flag,$phone_number,$exchange_rate,$expire_days,$expire_date,$billType,$bankName){

        $data = DB::table('payments')->where(['reference'=>$invoice,'id'=>$paymentId])->first();
        if (!empty($data)){

            $payment = Payment::find($paymentId);

            if (!empty($payment)){

                $payment->section_id = $sc;
                $payment->summary = $comma_separated;
                $payment->booking_from = $flag;
                $payment->re_assessment_from = null;
                $payment->phone_number = $phone_number;
                $payment->exchange_rate = $exchange_rate;
                $payment->bl_exchange_rate = $exchange_rate;
                $payment->expire_days = $expire_days;
                $payment->expire_date = $expire_date;
                $payment->billType = $billType;
                $payment->transferBank = $bankName;
                $payment->save();

                return $payment;

            }else{
                return null;
            }
        }else{
            return null;
        }

    }

    public static function savePayment($customer_id,$temp_payment_id,$total_amount,$account_code,$currency,$company_number,
                                       $invoice,$re_assessment_description,$bookingId,$calculationType,$licenceType,
                                       $billPhoneNumber,$entityType,$regDate){

        $data = Payment::where(['temp_payment_id'=>$temp_payment_id])->first();
        if (empty($data)){
            $payment = new Payment();
            $payment->bookingId = $bookingId;
            $payment->user_id = Auth::user()->id;
            $payment->customer_id =$customer_id;
            $payment->temp_payment_id = $temp_payment_id;
            $payment->billAmount = $total_amount;
            $payment->cheque_amount = '';
            $payment->payment_type = 'Cash';
            $payment->cheque_amount = '';
            $payment->date_of_payment = date('d/m/Y',strtotime(date('Y-m-d')));
            $payment->month = date('m');
            $payment->year = date('Y');
            $payment->account_code = $account_code;
            $payment->account = $account_code;
            $payment->pay_type = 'none';
            $payment->currency = $currency;
            $payment->app_print = 'no';
            $payment->regno = $company_number;
            $payment->reg_date = $regDate;
            $payment->invoice = $invoice;
            $payment->reference = $invoice;
            $payment->re_assessment_description = $re_assessment_description;
            $payment->add_date = date('Y-m-d');
            $payment->calculationType = $calculationType;
            $payment->licenceType = $licenceType;
            $payment->phone_number = $billPhoneNumber;
            $payment->entityType = $entityType;
            $payment->save();
        }else{
            $payment = $data;
        }


        return $payment;
    }

    public static function getReceiptRecords($flag, $fromDate = null, $toDate = null){
        $records = array();
        if ($fromDate != null && $toDate != null){
            $fromDate = date('Y-m-d 00:00:00', strtotime($fromDate));
            $toDate = date('Y-m-d 23:59:59', strtotime($toDate));
        }

        if (strtolower($flag) == 'individual'){

            $records = Payment::where(['payments.accountantId'=>Auth::user()->id,'isPaid'=>1]);

            if ($fromDate != null && $toDate != null){
                $records = $records->whereBetween('printedDate', array($fromDate,$toDate));
            }

            $records = $records->orderBy('payments.id','DESC')->get();

        }else{
            $records = Payment::where(['isPaid'=>1])->orderBy('payments.id','DESC');

            if ($fromDate != null && $toDate != null){
                $records = $records->whereBetween('printedDate', array($fromDate,$toDate));
            }

            $records = $records->get();
        }

        return $records;

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
