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

class PrintingController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function printBillPayment(Request $request){

        $payment_id = decrypt($request->payment_id);
        $type = $request->type;

        if (!empty($payment_id)){
            $paymentInfo = Payment::find($payment_id);
            $customer = Customer::find($paymentInfo->customer_id);
            $payerName = $customer->customer_name;
            $applicantName = $payerName;

            $paymentItems = PaymentFee::getAssessmentItems($payment_id);

            $booking = Booking::getBookingInfo($paymentInfo->invoice);
            $expireDate = date('Y-m-d', strtotime($booking->expire_date));
            $customerName = $payerName;
            //$amount = $booking->amount;
            $amount = $booking->billAmount;
            $currency = $booking->currency;

            $user = User::find($paymentInfo->user_id);

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
                $view = 'invoice';
                $bankName = null;
            }elseif (strtolower($type) == 'nmb'){
                $view = 'billTransfer';
                $bankName = 'National Microfinance Bank';
            }elseif (strtolower($type) == 'crdb'){
                $view = 'billTransfer';
                $bankName = 'CRDB';
            }elseif (strtolower($type) == 'nbc'){
                $view = 'billTransfer';
                $bankName = 'National Bank of Commerce';
            }

            return view('assessment.invoice.'.$view)
                ->with(compact('paymentInfo','paymentItems','payerName','qrcodedata',
                    'amountInWords','applicantName','booking','user','bankName','type'))
                ->with('title','Print assessment');

        }else{
            return \redirect()->to('new-assessment')->with('title','New assessment');
        }

    }
}
