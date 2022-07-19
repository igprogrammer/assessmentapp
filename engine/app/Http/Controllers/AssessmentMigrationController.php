<?php

namespace App\Http\Controllers;

use App\Models\Payment\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssessmentMigrationController extends Controller
{
    public static function migration(){

        try {
            $payments = DB::connection('mysql')->table('payment')->where(['migrated'=>0])
                ->take(10)->get();

            if ($payments->isNotEmpty()){
                foreach ($payments as $payment){

                    $payment_id = $payment->payment_id;
                    $amount = $payment->amount;
                    $cheque_amount = $payment->cheque_amount;
                    $payment_type = $payment->payment_type;
                    $check_no = $payment->check_no;
                    $customer = $payment->customer;
                    $date_of_payment = date('Y-m-d', strtotime(str_replace('/','-',$payment->date_of_payment)));
                    $month = $payment->month;
                    $year = $payment->year;
                    $account_code = $payment->account_code;
                    $account = $payment->account;
                    $user_id = $payment->user_id;
                    $pay_type = $payment->pay_type;
                    if ($payment->currency == 'TShs'){
                        $currency = 'TZS';
                    }else{
                        $currency = 'USD';
                    }

                    $app_print = $payment->app_print;
                    $register = $payment->register;
                    $regno = $payment->regno;
                    $regdate = $payment->regdate;
                    $invoice = $payment->invoice;
                    $status = $payment->status ?? 0;
                    $re_assessment_description = $payment->re_assessment_description;
                    $reference = $payment->reference;
                    $isPaid = $payment->isPaid;
                    $payDate = $payment->payDate;
                    $bankName = $payment->bankName;
                    $billType = $payment->billType;
                    $transferBank = $payment->transferBank;
                    $serviceType = $payment->serviceType;

                    $checkData = DB::table('payments')->where(['account_code'=>$account_code,
                        'reference'=>$reference,'invoice'=>$invoice])->first();

                    if (empty($checkData)){

                        $booking = DB::connection('pgsql')->table('booking')->where(['reference'=>$reference])->first();

                        if (!empty($booking)){

                            $bookDate = $booking->book_date;
                            $sectionId = $booking->section_id;
                            $summary = $booking->summary;
                            $bookingFrom = $booking->booking_from;
                            $expireDate = $booking->expire_date;
                            $expireStatus = $booking->expire_status;
                            $phoneNumber = $booking->phone_number;
                            $exchangeRate = $booking->exchange_rate;
                            $blExchangeRate = $booking->bl_exchange_rate;
                            $expireDays = $booking->expire_days;
                            $regNo = $booking->regno;
                            $ischecked = $booking->ischecked;
                            $bookingId = $booking->booking_id;
                        }else{
                            $bookDate = null;
                            $sectionId = null;
                            $summary = null;
                            $bookingFrom = null;
                            $expireDate = null;
                            $expireStatus = null;
                            $phoneNumber = null;
                            $exchangeRate = null;
                            $blExchangeRate = null;
                            $expireDays = null;
                            $regNo = null;
                            $ischecked = null;
                            $bookingId = null;
                        }


                        $payInfo = new Payment();
                        $payInfo->billId = '';
                        $payInfo->bookingId = $bookingId;
                        $payInfo->user_id = $user_id;
                        $payInfo->temp_payment_id = null;
                        $payInfo->customer_id = $customer;

                        $pay = DB::connection('pgsql')->table('payment as p');

                        if ($sectionId == 8){
                            $pay->join('stamp_duty_receipt as r','r.payment_id','=','p.payment_id');
                        }else{
                            $pay->join('receipt as r','r.payment_id','=','p.payment_id');
                        }

                        $pay = $pay->where(['p.booking_id'=>$booking->booking_id])->first();

                        $receiptInfo = DB::connection('mysql_rec')->table('payment')->where(['ass_no'=>$invoice])->first();
                        if (!empty($receiptInfo)){
                            $accountantId = $receiptInfo->user_id;
                        }

                        if (!empty($pay)){
                            $receiptNo = $pay->receipt_number;
                            $bank_transaction_id = $pay->bank_transaction_id;
                            $mobile_transaction_id = $pay->mobile_transaction_id;
                            $bank_account_number = $pay->bank_account_number;
                            $PspReceiptNumber = $pay->bank_transaction_id;
                            $method = $pay->method;
                            $gepgStatus = 7101;
                            $paymentEntryDate = $pay->date;
                            $payMonth = date('m', strtotime($paymentEntryDate));
                            $payYear = date('Y', strtotime($paymentEntryDate));
                            $payrefid = $pay->payrefid;
                            $isPaid = 1;
                        }else{

                            $bank_transaction_id = null;
                            $mobile_transaction_id = null;
                            $bank_account_number = null;
                            $PspReceiptNumber = null;
                            $method = null;
                            $gepgStatus = null;
                            $paymentEntryDate = null;
                            $receiptNo = null;
                            $payMonth = null;
                            $payYear = null;
                            $payrefid = null;

                            if (!empty($receiptInfo)){
                                $receiptNo = $receiptInfo->receipt_number;
                                $payMonth = $receiptInfo->month;
                                $payYear = $receiptInfo->year;
                                $payrefid = null;
                                $isPaid = 1;
                            }else{
                                $isPaid = 0;
                            }
                        }

                        $entityType = self::entityType($account_code);
                        $payInfo->entityType = $entityType;
                        $payInfo->billAmount = $amount;
                        $payInfo->cheque_amount = $cheque_amount;
                        $payInfo->payment_type = $payment_type;
                        $payInfo->cheque_no = $check_no;
                        $payInfo->date_of_payment = $date_of_payment;
                        $payInfo->month = $month;
                        $payInfo->year = $year;
                        $payInfo->account_code = $account_code;
                        $payInfo->account = $account;
                        $payInfo->pay_type = $pay_type;
                        $payInfo->currency = $currency;
                        $payInfo->app_print = $app_print;
                        $payInfo->register = $register;
                        $payInfo->regno = $regno;
                        $payInfo->reg_date = $regdate;
                        $payInfo->invoice = $invoice;
                        $payInfo->status = $status;
                        $payInfo->re_assessment_description = $reference;
                        $payInfo->reference = $reference;
                        $payInfo->add_date = date('Y-m-d', strtotime($date_of_payment));
                        $payInfo->calculationType = 1;
                        $payInfo->licenceType = 1;
                        $payInfo->created_at = date('Y-m-d 00:00:00', strtotime($date_of_payment));
                        $payInfo->updated_at = date('Y-m-d 00:00:00', strtotime($date_of_payment));
                        $payInfo->book_date = $bookDate;
                        $payInfo->expire_days = $expireDays;
                        $payInfo->expire_date = $expireDate;
                        $payInfo->expireStatus = $expireStatus;
                        $payInfo->section_id =$sectionId;
                        $payInfo->summary = $summary;
                        $payInfo->booking_from = $bookingFrom;
                        $payInfo->re_assessment_from = $re_assessment_description;
                        $payInfo->controlNumber = $invoice;
                        $payInfo->phone_number = $phoneNumber;
                        $payInfo->exchange_rate = $exchangeRate;
                        $payInfo->bl_exchange_rate = $blExchangeRate;
                        $payInfo->billType = $billType;
                        $payInfo->transferBank = $transferBank;
                        $payInfo->isPaid = $isPaid;
                        $payInfo->payDate = $payDate;
                        $payInfo->bankName = $bankName;
                        $payInfo->receiptNo = '';
                        $payInfo->serviceType = $serviceType;
                        $payInfo->isChecked = $ischecked;
                        $payInfo->payPhoneNumber = $phoneNumber;
                        $payInfo->paidAmount = $amount;
                        $payInfo->bank_transaction_id = $bank_transaction_id;
                        $payInfo->PspReceiptNumber = $PspReceiptNumber;
                        $payInfo->method = $method;
                        $payInfo->gepgStatus = $gepgStatus;
                        $payInfo->paymentEntryDate = $paymentEntryDate;
                        $payInfo->payMonth = $payMonth;
                        $payInfo->payYear = $payYear;
                        $payInfo->printed = 1;
                        $payInfo->PayRefId = $payrefid;
                        $payInfo->accountantId = $accountantId ?? null;
                        $payInfo->printedDate = date('Y-m-d 00:00:00', strtotime($date_of_payment));
                        $payInfo->isBillPrinted = 1;
                        $payInfo->isReceiptPrinted = 1;
                        dd($payInfo);
                        $payInfo->save();

                        //update
                        DB::connection('mysql_b')->table('payment')->where(['payment_id'=>$payment_id])->update(array('migrated'=>1));



                    }else{
                        //update
                        DB::connection('mysql_b')->table('payment')->where(['payment_id'=>$payment_id])->update(array('migrated'=>1));
                    }


                    $message = "Payment id: ".$payment->payment_id.' successfully migrated';
                    Log::channel('migration')->info($message);
                }
            }else{
                $message = "No record to migrate @ ".date('d-m-Y H:i:s');
                Log::channel('migration')->info($message);
            }
        }catch (\Exception $exception){
            $message = $exception->getMessage().' on line '.$exception->getLine().' of file '.$exception->getFile();
            dd($message);
        }


    }

    public static function entityType($accnt_code){
        if ($accnt_code == '440320') {
            $type = 'TM';
        }elseif($accnt_code == '440322'){
            $type = 'PT';
        }elseif($accnt_code == '440331'){
            $type = 'CMP';
        }elseif($accnt_code == '440332'){
            $type = 'BN';
        }elseif($accnt_code == '440341'){
            $type = 'IL';
        }elseif($accnt_code == '440300'){
            $type = 'OT';
        }
        elseif($accnt_code == '440343'){
            $type = 'CMP';
        }
        elseif($accnt_code == '440350'){
            $type = 'OT';
        }
        elseif($accnt_code == '440342'){
            $type = 'BL';
        }

        return $type;
    }
}
