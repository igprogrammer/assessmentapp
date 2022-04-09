<?php

namespace App\Http\Controllers\BillPayment;

use App\Http\Controllers\Controller;
use App\Models\GepgBillResponse;
use App\Models\GepgCode;
use App\Models\IncomingControlNumber;
use App\Models\IncomingPayment;
use App\Models\Payment\Payment;
use App\Models\TransError;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillAndPaymentController extends Controller
{



    public static function getReconciliationData(){

        $xml = file_get_contents("php://input");

        $simpleXml = simplexml_load_string($xml);
        $json = json_encode($simpleXml);//parse the string to json

        $reqcontent = json_decode($json,true);//convert the json to a php array variable


        if (!empty($reqcontent)){

            $SpReconcReqId = $reqcontent['ReconcBatchInfo']['SpReconcReqId'];
            $SpCode = $reqcontent['ReconcBatchInfo']['SpCode'];
            $SpName = $reqcontent['ReconcBatchInfo']['SpName'];
            $ReconcStsCode = $reqcontent['ReconcBatchInfo']['ReconcStsCode'];


            DB::connection('sqlsrv_recon')->table('reconciliationContents')->insert(array(
                'SpCode'=>$SpCode,
                'SpName'=>$SpName,
                'ReconcStsCode'=>$ReconcStsCode,
                'responseContent'=>$json,
                'requestId'=>$SpReconcReqId,
                'createdDate'=>Carbon::now('Africa/Dar_es_Salaam')
            ));

            /*============= acknowledge to GePG that the request is Okay===============*/
            $response_array = array('7101'=>'ReconcStsCode');
            $xml = new \SimpleXMLElement('<gepgSpReconcRespAck/>');
            array_walk_recursive($response_array, array ($xml, 'addChild'));
            echo $xmldata =  explode("\n",$xml->asXML(),2)[1];

            /*==============end ack to GePG==============================*/


        }else{


            /*============= acknowledge to GePG that the request is Okay===============*/
            $response_array = array('7101'=>'ReconcStsCode');
            $xml = new \SimpleXMLElement('<gepgSpReconcRespAck/>');
            array_walk_recursive($response_array, array ($xml, 'addChild'));
            echo $xmldata =  explode("\n",$xml->asXML(),2)[1];

            /*==============end ack to GePG==============================*/


        }


    }

    /**
     * @return void
     * Receive reconciliation requests from reports system and send to GePG
     */
    public function receiveReconRequest(){

        try{

            $xml = file_get_contents("php://input");

            $simpleXml = simplexml_load_string($xml);
            $json = json_encode($simpleXml);//parse the string to json

            $reqcontent = json_decode($json,true);//convert the json to a php array variable


            $requestId = null;

            if (!empty($reqcontent)){
                $requestId = $reqcontent['BillTrxInf']['BillId'];
            }



            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, reconRequestUrl());
            curl_setopt( $ch, CURLOPT_POST, true );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml','Gepg-Com: default.sp.in'));
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, "$xml" );

            $result = curl_exec($ch);



            //save the xml response into the database for references
            DB::connection('sqlsrv_recon')->table('reconRequestResponse')->insert(array(
                'responseContent'=>$result,
                'requestId'=>$requestId,
                'createdDate'=>Carbon::now('Africa/Dar_es_Salaam')
            ));

            /*============= acknowledge to GePG that the request is Okay===============*/
            $response_array = array('7101'=>'ReconcStsCode');
            $xml = new \SimpleXMLElement('<gepgSpReconcRespAck/>');
            array_walk_recursive($response_array, array ($xml, 'addChild'));
            echo $xmldata =  explode("\n",$xml->asXML(),2)[1];

            /*==============end ack to GePG==============================*/




            curl_close($ch);

        }catch(\Exception $exception){
            $message = $exception->getMessage().' on line number '.$exception->getLine().' of file '.$exception->getFile();
            TransError::saveTransError($message);

        }



    }

    /**
     * @return void
     * Receive bill requests from bo system and send to GePG
     */
    public function receive_bo_bill(){

        try{

            $xml = file_get_contents("php://input");

            $simpleXml = simplexml_load_string($xml);
            $json = json_encode($simpleXml);//parse the string to json

            $bill_content = json_decode($json,true);//convert the json to a php array variable


            $booking_id = null;

            if (!empty($bill_content)){
                $booking_id = $bill_content['BillTrxInf']['BillId'];
            }



            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, billRequestUrl());
            curl_setopt( $ch, CURLOPT_POST, true );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml','Gepg-Com: default.sp.in'));
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, "$xml" );

            $result = curl_exec($ch);



            //save the xml response into the database for references
            GepgBillResponse::saveGepgBillResponse($booking_id,$result);

            /*============= acknowledge to GePG that the request is Okay===============*/
            $response_array = array('7101'=>'trxStsCode');
            $xml = new \SimpleXMLElement('<gepgBillSubRespAck/>');
            array_walk_recursive($response_array, array ($xml, 'addChild'));
            echo $xmldata =  explode("\n",$xml->asXML(),2)[1];

            /*==============end ack to GePG==============================*/




            curl_close($ch);

        }catch(\Exception $exception){
            $message = $exception->getMessage().' on line number '.$exception->getLine().' of file '.$exception->getFile();
            TransError::saveTransError($message);
        }



    }

    /**
     * @return void
     * Receive payment notifications from GePG
     */
    public function payment()
    {

        $data = file_get_contents("php://input");

        $simpleXml = simplexml_load_string($data);
        $json = json_encode($simpleXml);//parse the string to json

        $payment_content = json_decode($json,true);//convert the json to a php array variable



        //create payment object and get values
        foreach ($payment_content as $object){
            $bookingId = $object['BillId'];
            $xmlContent = $data;
            $TrxId = $object['TrxId'];
            $SpCode = $object['SpCode'];
            $PayRefId = $object['PayRefId'];
            $PayCtrNum = $object['PayCtrNum'];
            $BillAmt = $object['BillAmt'];
            $PaidAmt = $object['PaidAmt'];
            $BillPayOpt = $object['BillPayOpt'];
            $CCy = $object['CCy'];
            $TrxDtTm = $object['TrxDtTm'];
            $UsdPayChnl = $object['UsdPayChnl'];
            $PyrCellNum = $object['PyrCellNum'];
            $PyrEmail = $object['PyrEmail'];
            $PyrName = $object['PyrName'];
            $PspReceiptNumber = $object['PspReceiptNumber'];
            $PspName = $object['PspName'];


            //insert into incoming payments
            $checkIncomingPayment = IncomingPayment::getIncomingPaymentInfo($bookingId);

            if(empty($checkIncomingPayment)){
                $message = 'success';

                IncomingPayment::saveIncomingPayment($bookingId,$xmlContent,$TrxId,$SpCode,$PayRefId,$PayCtrNum,$BillAmt,$PaidAmt,
                    $BillPayOpt,$CCy,$TrxDtTm,$UsdPayChnl,$PyrCellNum,$PyrEmail,$PyrName,
                    $PspReceiptNumber,$PspName,$message);

                /*============= acknowledge to GePG that the response has been received===============*/
                $response_array = array('7101'=>'TrxStsCode');
                $xml = new \SimpleXMLElement('<gepgPmtSpInfoAck/>');
                array_walk_recursive($response_array, array ($xml, 'addChild'));
                echo $xmldata =  explode("\n",$xml->asXML(),2)[1];
                /*==============end ack to GePG==============================*/

            }else{

                /*============= acknowledge to GePG that the is already received===============*/
                $response_array = array('7101'=>'TrxStsCode');
                $xml = new \SimpleXMLElement('<gepgPmtSpInfoAck/>');
                array_walk_recursive($response_array, array ($xml, 'addChild'));
                echo $xmldata =  explode("\n",$xml->asXML(),2)[1];
                /*==============end ack to GePG==============================*/




            }


        }



    }

    /**
     * @return void
     * Function to get bill and control numbers notifications from GePG
     */
    public function receiveBillRequestResponse(){

        $data = file_get_contents("php://input");

        $simpleXml = simplexml_load_string($data);
        $json = json_encode($simpleXml);//parse the string to json
        $objectData = json_decode($json,true);//convert the json to a php array variable

        foreach($objectData as $obj){

            $bookingId = $obj['BillId'];
            $PayCntrNum = $obj['PayCntrNum'];
            $TrxSts = $obj['TrxSts'];
            $TrxStsCode = $obj['TrxStsCode'];
            $message = 'received';


            //check if control number has been received
            $checkIncomingControlNumber = IncomingControlNumber::getIncomingControlNumberInfo($bookingId);
            if (!empty($checkIncomingControlNumber)){

                /*============= acknowledge to GePG that the control number has been received is Okay===============*/
                $response_array = array('7101'=>'TrxStsCode');
                $xml = new SimpleXMLElement('<gepgBillSubRespAck/>');
                array_walk_recursive($response_array, array ($xml, 'addChild'));
                echo $xmldata =  explode("\n",$xml->asXML(),2)[1];
                /*==============end ack to GePG==============================*/

            }else{

                /*============= acknowledge to GePG that the control number has been received===============*/
                $response_array = array('7101'=>'TrxStsCode');
                $xml = new SimpleXMLElement('<gepgBillSubRespAck/>');
                array_walk_recursive($response_array, array ($xml, 'addChild'));
                echo $xmldata =  explode("\n",$xml->asXML(),2)[1];
                /*==============end ack to GePG==============================*/

                //save incoming control number
                IncomingControlNumber::saveIncomingControlNumber($data,$message,$bookingId);

                //save gepg response codes
                GepgCode::saveCodeResponse($bookingId,$PayCntrNum,$TrxSts,$TrxStsCode);

            }

            sleep(3);

            if($TrxStsCode == 7101 && $PayCntrNum != 0 && $TrxSts='GS' && !empty($bookingId)){

                //check if the booking exists in the database
                $paymentInfo = Payment::getPaymentInfoByBookingId($bookingId);
                $bookingFrom = $paymentInfo->booking_from;
                $reference = $paymentInfo->reference;
                $controlNumber = $paymentInfo->controlNumber;

                $payInfo = Payment::getPaymentInfoByReference($reference);
                if (!empty($payInfo)){

                    if (empty($controlNumber)){

                        $cNumber = Payment::updateControlNumber($bookingId,$PayCntrNum);

                        if (!empty($cNumber)){
                            $msg = 'GS';
                        }else{
                            $msg = 'GF';
                        }

                        IncomingControlNumber::updateIncomingControlNumberMessage($bookingId,$msg);

                    }

                }

            }else{
                $msg = 'GF';
                IncomingControlNumber::updateIncomingControlNumberMessage($bookingId,$msg);
            }
        }


    }

}
