<?php

namespace App\Http\Controllers\Printing;

use App\Helpers\CurrencyNumberToWordConverter;
use App\Http\Controllers\Controller;
use App\Models\Booking\Booking;
use App\Models\Customer\Customer;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentFee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PrintingController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function printBillPayment(Request $request){

        $payment_id = decrypt($request->payment_id);
        $type = $request->type;
        $isCopyReceipt = 0;
        $isCopyBill = 0;


        if (!empty($payment_id)){

            $paymentInfo = Payment::find($payment_id);
            $customer = Customer::find($paymentInfo->customer_id);
            $payerName = $customer->customer_name;
            $applicantName = $payerName;
            $user = User::find($paymentInfo->user_id);

            $paymentItems = PaymentFee::getAssessmentItems($payment_id);
            if ($type == 'receipt'){

                if (empty($paymentInfo->accountantId)){
                    $payment = Payment::setAccountantId($payment_id,1);
                    if ($payment){
                        $accountant = User::find($payment->accountantId);
                        $isCopyReceipt = 0;
                    }
                }else{
                    $accountant = User::find($paymentInfo->accountantId);
                    $isCopyReceipt = 1;
                }


                $amountInWords = trim(CurrencyNumberToWordConverter::convertNumber($paymentInfo->paidAmount,$paymentInfo->currency));

                $view = 'payment.payment_receipt';
                $title = "Print receipt";
                $qrcodedata = null;
                $booking = $paymentInfo;
                $bankName = $paymentInfo->bankName;
            }else{

                if (!empty($paymentInfo->accountantId)){
                    $accountant = User::find($paymentInfo->accountantId);
                }else{
                    $accountant = null;
                }

                if ($paymentInfo->isBillPrinted == 0 || empty($paymentInfo->isBillPrinted)){
                    $payment = Payment::updateBillPrinting($payment_id,1);
                    $isCopyBill = 0;
                }else{
                    $isCopyBill = 1;
                }

                $booking = Booking::getBookingInfo($paymentInfo->invoice);
                $expireDate = date('Y-m-d', strtotime($booking->expire_date));
                $customerName = $payerName;
                //$amount = $booking->amount;
                $amount = $booking->billAmount;
                $currency = $booking->currency;

                $data = [
                    "opType"=>"20000",
                    "shortCode"=>"0010010000",
                    "billReference"=>"$booking->reference",
                    "amount"=>"$amount",
                    "billCcy"=>"$currency",
                    "billExprDt"=>"$expireDate",
                    "billPayOpt"=>"3",
                    "billRsv01"=>"Business Registrations and Licensing Agency|$customerName"
                ];

                $amountInWords = trim(CurrencyNumberToWordConverter::convertNumber($amount,$currency));


                $qrcodedata = json_encode($data, true);

                if (strtolower($type) == 'normal'){
                    $view = 'invoice.invoice';
                    $bankName = null;
                }elseif (strtolower($type) == 'nmb'){
                    $view = 'invoice.billTransfer';
                    $bankName = 'National Microfinance Bank';
                }elseif (strtolower($type) == 'crdb'){
                    $view = 'invoice.billTransfer';
                    $bankName = 'CRDB';
                }elseif (strtolower($type) == 'nbc'){
                    $view = 'invoice.billTransfer';
                    $bankName = 'National Bank of Commerce';
                }

                $title = 'Print assessment';

            }

            //get payment info after updates
            $paymentInfo = Payment::find($payment_id);

            return view('assessment.'.$view)
                ->with(compact('paymentInfo','paymentItems','payerName','qrcodedata',
                    'amountInWords','applicantName','booking','user','bankName','type','accountant','isCopyReceipt','isCopyBill'))
                ->with('title',$title);

        }else{
            return \redirect()->to('new-assessment')->with('title','New assessment');
        }

    }
}
