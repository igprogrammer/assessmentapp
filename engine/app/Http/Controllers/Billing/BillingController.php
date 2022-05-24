<?php

namespace App\Http\Controllers\Billing;

use App\Helpers\JsonToArrayConverter;
use App\Helpers\ServerListener;
use App\Http\Controllers\Assessment\GeneralController;
use App\Http\Controllers\Controller;
use App\Models\Assessment\ErrorsLog;
use App\Models\Assessment\EventLog;
use App\Models\Assessment\Fee;
use App\Models\Assessment\FeeItem;
use App\Models\Billing\Billing;
use App\Models\Billing\BillPayOption;
use App\Models\Booking\Booking;
use App\Models\Customer\Customer;
use App\Models\GepgBillResponse;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\ArrayToXml\ArrayToXml;
use function App\Http\Controllers\getDataString;
use function App\Http\Controllers\getSignatureString;

class BillingController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public static function reqCn(Request $request){
        dd($request->paymentId);
    }

    public static function receiveAndUpdateBillControlNumber($response,$reference=null,$invoice=null,$billId=null,$message=null){
        if ($response == 1){
            $result = 1;
            $message = " Successfully received Payment Control number";
            $status = 'Success';

            $payment = Payment::getPaymentInfoByReference($reference);

            if (!empty($payment)){
                //update billing with the incoming control number
                Billing::updateControlNumber($billId,$invoice);
            }
        }elseif ($response == 0){
            $result = 0;
            $message = " Failed to generate and receive Payment Control number";
            $status = 'Fail';
        }elseif ($response == 3){
            $result = 3;
            $message = $message;
            $status = 'Fail';
        }else{
            $result = 0;
            $message = " Failed to get Control number due to invalid or missing billing information";
            $status = 'Fail';
        }

        return response()->json(['result'=>$result,'message'=>$message,'status'=>$status]);
    }

    public static function requestControlNumber(Request $request){

        try {
            $paymentId = decrypt($request->paymentId);
            $message = "Failed to get control number from GePG";

            $payment = Payment::find($paymentId);
            $bill = Billing::getBill($payment->reference);
            if (!empty($bill)){

                if ((int)$payment->invoice < initialControlNumber())

                $data = BillingController::generateBill($bill->reference,$paymentId);
                $response = $data->getData()->result;
                $message = $data->getData()->message;

                //$response = BillingController::generateBill($bill->reference,$paymentId);

            }else{
                $response = 2;
            }

            //call function to save and update control number
            $booking = Booking::getBookingInfoByReference($payment->reference);
            $data = self::receiveAndUpdateBillControlNumber($response,$payment->reference,$booking->invoice,$bill->billId,$message);

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
        $bookingInfo = Booking::getBookingData($invoice);
        $bookingId = $bookingInfo->bookingId;
        $payment = Payment::find($paymentId);

        //Put GePG processes here

        if(!empty($bookingInfo)){



            //start GePG process
            $customer = Customer::find($payment->customer_id);
            $name = $customer->customer_name;
            $section_id = $payment->section_id;

            /*========REPLACING APOSTROPHE WITH NONE EMPTY SPACE FOR PAYER NAME STARTS HERE*/
            $str=$name;
            $payer_name = str_replace("'", "", $str);
            /*========REPLACING APOSTROPHE WITH NONE EMPTY SPACE FOR PAYER NAME STARTS HERE*/

            $SpCodeVar = spCode();
            $SubSpCodeVar = subSpCode();
            $SpSysIdVar = spSysId();
            $exchange_rate = $bookingInfo->exchange_rate;

            $billEqvAmount = 0;
            if($bookingInfo->currency == 'USD'){
                $billEqvAmount = $bookingInfo->billAmount * $exchange_rate;
            }else{
                $billEqvAmount = $bookingInfo->billAmount;
            }

            $description = "BRELA Revenue";
            $billPayOptInfo = BillPayOption::getBillPayOpt();
            $payOpt = $billPayOptInfo->BillPayOpt ?? 3;


            $repo = [];
            $paymentFees = PaymentFee::getPaymentItems($paymentId);
            if (!empty($paymentFees)){

                foreach ($paymentFees as $paymentFee){

                    $fee_item_id = $paymentFee->fee_item_id;
                    $fee_item = FeeItem::find($fee_item_id);
                    $fee = Fee::find($fee_item->fee_id);

                    $gfs_code = $fee->gfs_code;


                    $billItemEqvAmount = 0;
                    if($bookingInfo->currency == 'USD'){
                        $billItemEqvAmount = $paymentFee->fee_amount * $exchange_rate;
                    }else{
                        $billItemEqvAmount = $paymentFee->fee_amount;
                    }


                    $data = [
                        'BillItem'=>[
                            'BillItemRef'=>$bookingInfo->invoice,
                            'UseItemRefOnPay'=>'N',
                            'BillItemAmt'=>$paymentFee->fee_amount,
                            'BillItemEqvAmt'=>$billItemEqvAmount,
                            'BillItemMiscAmt'=>0,
                            'GfsCode'=>$gfs_code
                        ]
                    ];

                    array_push($repo,$data);

                }



            }

            $dataArray = [
                'BillHdr'=>[
                    'SpCode'=>$SpCodeVar,
                    'RtrRespFlg'=>'true'
                ],
                'BillTrxInf'=>[
                    'BillId'=>$bookingInfo->bookingId,
                    'SubSpCode'=>$SubSpCodeVar,
                    'SpSysId'=>$SpSysIdVar,
                    'BillAmt'=>$bookingInfo->billAmount,
                    'MiscAmt'=>0,
                    'BillExprDt'=>date("Y-m-d\TH:i:s", strtotime($bookingInfo->expire_date)),
                    'PyrId'=>$payer_name,
                    'PyrName'=>$payer_name,
                    'BillDesc'=>$description,
                    'BillGenDt'=>date("Y-m-d\TH:i:s", strtotime($bookingInfo->book_date)),
                    'BillGenBy'=>$bookingInfo->booking_from,
                    'BillApprBy'=>$bookingInfo->booking_from,
                    'PyrCellNum'=>$bookingInfo->phone_number,
                    'PyrEmail'=>'usajili@brela.go.tz',
                    'Ccy'=>$bookingInfo->currency,
                    'BillEqvAmt'=>$billEqvAmount,
                    'RemFlag'=>'true',
                    'BillPayOpt'=>$payOpt,
                    'BillItems'=>$repo
                ]
            ];

            //call the function to listen to server,sign bill and send to GePG
            $requestType = 'bill';
            return self::sendBillContentToGePG(billRequestUrl(),$dataArray,$bookingId,$payment->reference,$requestType);

        }

        /*End GePG content*/
    }

    public static function sendBillContentToGePG($url,$billContent,$bookingId=null,$reference=null,$requestType=null,$reconRequestId=null){


        //return signed xml content
        $billContent = self::billContent($billContent,$bookingId,$requestType);
        $content = $billContent;

        //update bill xml content
        Billing::updateBill($bookingId,$reference,$billContent);

        //save the xml content into the database for references
        Booking::saveBillContent($bookingId,$billContent);


        $status = ServerListener::checkServerStatus($url);

        if (!$status){
            $message = "The GePG payment gateway :".$url." responded with error status 3 and it is not reachable at the moment,please try again later or contact System administrator";
            Log::channel('assessment-error')->info($message);
            $msg = "The Payment gateway is not reachable at the moment,please try again later or contact System administrator";
            EventLog::saveEvent(Auth::user()->username,'Billing','User',Auth::user()->name,'Fail','Request control number',$message,EventLog::getIpAddress(),EventLog::getMacAddress(),'BillingController','sendBillContentViaAssessmentSystem');
            return response()->json(['result'=>3,'message'=>$msg]);
        }

        //$url = $url.'api/receive-bill';
        $url = $url.'receive-bill';

        $req = curl_init();
        curl_setopt( $req, CURLOPT_URL, $url);
        curl_setopt( $req, CURLOPT_POST, true );
        curl_setopt( $req, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $req, CURLOPT_POSTFIELDS, "$billContent" );
        curl_setopt( $req, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));

        //Capture returned content from local gateway to GePG
        $requestResponse = curl_exec($req);
        $requestResponse = str_replace("\n","",$requestResponse);

        $response = self::isXMLContentValid($requestResponse,$version = '1.0', $encoding = 'utf-8');

        if ($response == true){

            $res = simplexml_load_string($requestResponse);
            $json = json_encode($res);//parse the string to json

            $res = json_decode($json,true);//convert the json to a php array variable

            if ($res['trxStsCode'] == '7101'){
                $result = 1;
                $message = 'Successfully received control number from GePG';
            }else{
                $result = 0;
                $message = 'Failed to received control number from GePG';
            }

        }
        else{
            $result = 0;
            $message = 'Failed to received control number from GePG';
        }

        return response()->json(['result'=>$result,'message'=>$message]);


    }

    public static function readPublicKeyStore($vdata,$vsignature){

        $readMessage = null;
        if (!$pcert_store = file_get_contents(pubKey())) {
            $message = "Error: Unable to read the public certificate file";
            $status = 201;
            return response()->json(['status'=>$status,'message'=>$message]);
        }
        else{

            //Read Certificate
            if (openssl_pkcs12_read($pcert_store,$pcert_info,"passpass")) {

                //Decode Received Signature String
                $rawsignature = base64_decode($vsignature);

                //Verify Signature and state whether signature is okay or not
                $ok = openssl_verify($vdata, $rawsignature, $pcert_info['extracerts']['0']);
                if ($ok == 1) {
                    $signatureMessageStatus = "The signature is Good";
                    $status = 1;
                } elseif ($ok == 0) {
                    $signatureMessageStatus = "The signature is Bad";
                    $status = 0;
                } else {
                    $signatureMessageStatus = "UGLY, Error checking signature";
                    $status = 0;
                }

            }else{
                $signatureMessageStatus = "Failed to read the public certificate store";
                $status = 0;
            }

            return response()->json(['status'=>0,'message'=>$signatureMessageStatus]);


        }



    }

    public static function billContent($billContent,$bookingId,$requestType){

        if ($requestType == 'bill'){
            $replacer = 'gepgBillSubReq';
            $arrayContent = $billContent;
        }elseif ($requestType == 'recon'){
            $replacer = 'gepgSpReconcReq';
            $arrayContent = $billContent;
        }

        $xmlContent = ArrayToXml::convert($arrayContent);
        $content = str_replace('root',$replacer,$xmlContent);

        $content = str_replace('<?xml version="1.0"?>','',$content);

        return $content;


    }

    function getDataString($inputstr,$datatag){
        $datastartpos = strpos($inputstr, $datatag);
        $dataendpos = strrpos($inputstr, $datatag);
        $data=substr($inputstr,$datastartpos - 1,$dataendpos + strlen($datatag)+2 - $datastartpos);
        return $data;
    }

    function getSignatureString($inputstr,$sigtag){
        $sigstartpos = strpos($inputstr, $sigtag);
        $sigendpos = strrpos($inputstr, $sigtag);
        $signature=substr($inputstr,$sigstartpos + strlen($sigtag)+1,$sigendpos - $sigstartpos -strlen($sigtag)-3);
        return $signature;
    }


    public static function isXMLFileValid($xmlFilename, $version = '1.0', $encoding = 'utf-8'){
        $xmlContent = file_get_contents($xmlFilename);
        return self::isXMLContentValid($xmlContent, $version, $encoding);
    }

    public static function isXMLContentValid($xmlContent, $version = '1.0', $encoding = 'utf-8')
    {

        if (trim($xmlContent) == '') {
            return false;
        }

        libxml_use_internal_errors(true);

        $doc = new \DOMDocument($version, $encoding);
        $doc->loadXML($xmlContent);

        $errors = libxml_get_errors();
        libxml_clear_errors();

        return empty($errors);
    }

}
