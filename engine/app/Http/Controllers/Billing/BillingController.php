<?php

namespace App\Http\Controllers\Billing;

use App\Helpers\ServerListener;
use App\Http\Controllers\Assessment\GeneralController;
use App\Http\Controllers\Controller;
use App\Models\Assessment\EventLog;
use App\Models\Assessment\Fee;
use App\Models\Assessment\FeeItem;
use App\Models\Billing\Billing;
use App\Models\Booking\Booking;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function receiveAndUpdateBillControlNumber($response,$reference=null,$invoice=null,$billId=null){
        if ($response == 1){
            $result = 1;
            $message = " Successfully received Payment Control number";
            $status = 'Success';

            $booking = Booking::getBookingInfoByReference($reference);

            if (!empty($booking)){
                //update billing with the incoming control number
                Billing::updateControlNumber($billId,$invoice);
            }
        }elseif ($response == 0){
            $result = 0;
            $message = " Failed to generate and receive Payment Control number";
            $status = 'Fail';
        }else{
            $result = 0;
            $message = " Failed to get Control number due to invalid or missing billing information";
            $status = 'Fail';
        }

        return response()->json(['result'=>$result,'message'=>$message,'status'=>$status]);
    }

    public function requestControlNumber(Request $request){

        try {
            $paymentId = $request->paymentId;

            $payment = Payment::find($paymentId);
            $bill = Billing::getBill($payment->reference);
            if (!empty($bill)){

                $data = BillingController::generateBill($bill->reference,$paymentId);
                $response = $data->getData()->result;
                $message = $data->getData()->message;

            }else{
                $response = 2;
            }

            //call function to save and update control number
            $booking = Booking::getBookingInfoByReference($payment->reference);
            $data = self::receiveAndUpdateBillControlNumber($response,$payment->reference,$booking->invoice,$bill->billId);

            $result = $data->getData()->result;
            $message = $data->getData()->message;
            $status = $data->getData()->status;

            EventLog::saveEvent(Auth::user()->username,'System access','User',Auth::user()->name,$status,'Request control number',$message,EventLog::getIpAddress(),EventLog::getMacAddress(),'BillingController','requestControlNumber');
            Log::channel('assessment')->info($message);

            sleep(5);

            return response()->json(['success'=>$result,'message'=>$message]);



        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'AssessmentController','requestControlNumber','assessment-error');
            return response()->json(['success'=>0,'message'=>$message]);
            return redirect()->back()->with('error-message',$message);
        }
    }

    public static function generateBill($invoice,$paymentId){
        $booking_info = Booking::getBookingData($invoice);
        $booking_id = $booking_info->booking_id;
        $payment = Payment::find($paymentId);

        //Put GePG processes here

        if(!empty($booking_info)){



            //start GePG process
            $bookingName = Booking::getInvoiceName($booking_id);
            $name = $bookingName->name;
            $section_id = $booking_info->section_id;

            /*========REPLACING APOSTROPHE WITH NONE EMPTY SPACE FOR PAYER NAME STARTS HERE*/
            $str=$name;
            $payer_name = str_replace("'", "", $str);
            /*========REPLACING APOSTROPHE WITH NONE EMPTY SPACE FOR PAYER NAME STARTS HERE*/

            $SpCodeVar='SP135';
            $SubSpCodeVar = '1001';
            $SpSysIdVar='BRELA001';
            $exchange_rate=$booking_info->exchange_rate;

            $bill_eqv_amount = 0;
            if($booking_info->currency == 'USD'){
                $bill_eqv_amount = $booking_info->amount * $exchange_rate;
            }else{
                $bill_eqv_amount = $booking_info->amount;
            }


            /*==========start bill description=====*/
            $invoice = $booking_info->invoice;
            //select from payment

            $payment = Payment::find($paymentId);
            $description = "BRELA Revenue";

            /*==========end bill description=====*/

            header('Content-Type: application/xml');
            //Creates XML string and XML document using the DOM

            $dom = new \DOMDocument('1.0');
            $gepgBillSubReq = $dom->appendChild($dom->createElement('gepgBillSubReq'));
            $BillHdr = $gepgBillSubReq->appendChild($dom->createElement('BillHdr'));

            $SpCode = $BillHdr->appendChild($dom->createElement('SpCode'));
            $SpCode->appendChild($dom->createTextNode($SpCodeVar));
            $RtrRespFlg = $BillHdr->appendChild($dom->createElement('RtrRespFlg'));
            $RtrRespFlg->appendChild($dom->createTextNode('true'));

            $BillTrxInf = $gepgBillSubReq->appendChild($dom->createElement('BillTrxInf'));
            $BillId = $BillTrxInf->appendChild($dom->createElement('BillId'));
            $BillId->appendChild($dom->createTextNode($booking_info->booking_id));
            $SubSpCode = $BillTrxInf->appendChild($dom->createElement('SubSpCode'));
            $SubSpCode->appendChild($dom->createTextNode($SubSpCodeVar));
            $SpSysId = $BillTrxInf->appendChild($dom->createElement('SpSysId'));
            $SpSysId->appendChild($dom->createTextNode($SpSysIdVar));
            $BillAmt = $BillTrxInf->appendChild($dom->createElement('BillAmt'));
            $BillAmt->appendChild($dom->createTextNode($booking_info->amount));
            $MiscAmt = $BillTrxInf->appendChild($dom->createElement('MiscAmt'));
            $MiscAmt->appendChild($dom->createTextNode('0'));
            $BillExprDt = $BillTrxInf->appendChild($dom->createElement('BillExprDt'));
            $BillExprDt->appendChild($dom->createTextNode(date("Y-m-d\TH:i:s", strtotime($booking_info->expire_date))));
            $PyrId = $BillTrxInf->appendChild($dom->createElement('PyrId'));
            $PyrId->appendChild($dom->createTextNode($payer_name));
            $PyrName = $BillTrxInf->appendChild($dom->createElement('PyrName'));
            $PyrName->appendChild($dom->createTextNode($payer_name));
            $BillDesc = $BillTrxInf->appendChild($dom->createElement('BillDesc'));
            $BillDesc->appendChild($dom->createTextNode($description));
            $BillGenDt = $BillTrxInf->appendChild($dom->createElement('BillGenDt'));
            $BillGenDt->appendChild($dom->createTextNode(date("Y-m-d\TH:i:s", strtotime($booking_info->book_date))));
            $BillGenBy = $BillTrxInf->appendChild($dom->createElement('BillGenBy'));
            $BillGenBy->appendChild($dom->createTextNode($booking_info->booking_from));
            $BillApprBy = $BillTrxInf->appendChild($dom->createElement('BillApprBy'));
            $BillApprBy->appendChild($dom->createTextNode($booking_info->booking_from));
            $PyrCellNum = $BillTrxInf->appendChild($dom->createElement('PyrCellNum'));
            $PyrCellNum->appendChild($dom->createTextNode($booking_info->phone_number));
            $PyrEmail = $BillTrxInf->appendChild($dom->createElement('PyrEmail'));
            $PyrEmail->appendChild($dom->createTextNode('usajili@brela.go.tz'));
            $Ccy = $BillTrxInf->appendChild($dom->createElement('Ccy'));
            $Ccy->appendChild($dom->createTextNode($booking_info->currency));
            $BillEqvAmt = $BillTrxInf->appendChild($dom->createElement('BillEqvAmt'));
            $BillEqvAmt->appendChild($dom->createTextNode($bill_eqv_amount));
            $RemFlag = $BillTrxInf->appendChild($dom->createElement('RemFlag'));
            $RemFlag->appendChild($dom->createTextNode('true'));
            $BillPayOpt = $BillTrxInf->appendChild($dom->createElement('BillPayOpt'));
            $BillPayOpt->appendChild($dom->createTextNode('3'));
            $BillItems = $BillTrxInf->appendChild($dom->createElement('BillItems'));


            //select from payment
            $payment_fees = PaymentFee::where('payment_id','=',$paymentId)->get();
            if (!empty($payment_fees)){

                foreach ($payment_fees as $payment_fee){

                    $fee_item_id = $payment_fee->fee_item_id;
                    $fee_item = FeeItem::find($fee_item_id);
                    $fee = Fee::find($fee_item->fee_id);

                    $gfs_code = $fee->gfs_code;


                    $bill_item_eqv_amount = 0;
                    if($booking_info->currency == 'USD'){
                        $bill_item_eqv_amount = $payment_fee->fee_amount * $exchange_rate;
                    }else{
                        $bill_item_eqv_amount = $payment_fee->fee_amount;
                    }

                    $BillItem = $BillItems->appendChild($dom->createElement('BillItem'));
                    $BillItemRef = $BillItem->appendChild($dom->createElement('BillItemRef'));
                    $BillItemRef->appendChild($dom->createTextNode($booking_info->invoice));
                    $UseItemRefOnPay = $BillItem->appendChild($dom->createElement('UseItemRefOnPay'));
                    $UseItemRefOnPay->appendChild($dom->createTextNode('N'));

                    $BillItemAmt = $BillItem->appendChild($dom->createElement('BillItemAmt'));
                    $BillItemAmt->appendChild($dom->createTextNode($payment_fee->fee_amount));

                    $BillItemEqvAmt = $BillItem->appendChild($dom->createElement('BillItemEqvAmt'));
                    $BillItemEqvAmt->appendChild($dom->createTextNode($bill_item_eqv_amount));

                    $BillItemMiscAmt = $BillItem->appendChild($dom->createElement('BillItemMiscAmt'));
                    $BillItemMiscAmt->appendChild($dom->createTextNode('0'));
                    $GfsCode = $BillItem->appendChild($dom->createElement('GfsCode'));
                    $GfsCode->appendChild($dom->createTextNode($gfs_code));



                }



            }

            //generate xml
            $dom->formatOutput = true; // set the formatOutput attribute of
            // domDocument to true
            // save XML as string or file
            $xml = $dom->saveXML();

            //update bill xml content
            Billing::updateBill($booking_id,$payment->reference,$xml);

            //save the xml content into the OBRS database for references
            Booking::saveBillContentToObrs($booking_id,$xml);


            //call the function to send bill vi assessment system which is connected to GePG
            return self::sendBillContentToGePG(assessment_url(),$xml);

        }

        /*End GePG content*/
    }



    public static function sendBillContentToGePG($url,$billContent){

        $status = ServerListener::checkServerStatus($url);

        if (!$status){
            $message = "The Assessment GePG server :".$url." is not reachable at the moment,please try again later or contact System administrator";
            Log::channel('assessment-error')->info($message);
            $msg = "The Payment gateway is not reachable at the moment,please try again later or contact System administrator";
            EventLog::saveEvent(Auth::user()->username,'Billing','User',Auth::user()->name,'Fail','Request control number',$message,EventLog::getIpAddress(),EventLog::getMacAddress(),'BillingController','sendBillContentViaAssessmentSystem');
            return response()->json(['result'=>0,'message'=>$msg]);
        }

        //$billUrl = $url.'receive-bo-bill';

        $req = curl_init();
        curl_setopt( $req, CURLOPT_URL, $url);
        curl_setopt( $req, CURLOPT_POST, true );
        curl_setopt( $req, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt( $req, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $req, CURLOPT_POSTFIELDS, "$billContent" );

        $result = curl_exec($req);

        /*============= acknowledge to GePG that the request is Okay===============*/
        $response_array = array('7101'=>'trxStsCode');
        $xml = new \SimpleXMLElement('<gepgBillSubRespAck/>');
        array_walk_recursive($response_array, array ($xml, 'addChild'));
        echo $xmldata =  explode("\n",$xml->asXML(),2)[1];

        /*==============end ack to GePG==============================*/

        curl_close($req);

        $res = simplexml_load_string($result);
        $json = json_encode($res);//parse the string to json

        $res = json_decode($json,true);//convert the json to a php array variable

        if ($res['trxStsCode'] == '7101'){
            $result = 1;
            $message = 'Success received control number from GePG';
        }else{
            $result = 0;
            $message = 'Failed to received control number from GePG';
        }

        return response()->json(['result'=>$result,'message'=>$message]);


    }

}
