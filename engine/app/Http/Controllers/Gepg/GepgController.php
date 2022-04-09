<?php

namespace App\Http\Controllers\Gepg;

use App\Http\Controllers\Controller;
use App\Models\GepgBillResponse;
use Illuminate\Http\Request;

class GepgController extends Controller
{
    public static function gepgBillSendingRoute($url,$billContent,$requestType,$bookingId,$reconRequestId){


        $req = curl_init();
        curl_setopt( $req, CURLOPT_URL, $url);
        //curl_setopt($req, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt( $req, CURLOPT_POST, true );
        curl_setopt( $req, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $req, CURLOPT_POSTFIELDS, "$billContent" );
        curl_setopt( $req, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        //curl_setopt( $req, CURLOPT_HTTPHEADER, array('Content-Type: application/xml','Gepg-Com: default.sp.in','Gepg-Code:SP116'));
        //curl_setopt($req, CURLOPT_TIMEOUT, 50);
        //curl_setopt($req, CURLOPT_CONNECTTIMEOUT, 50);

        //Capture returned content from GePG
        $requestResponse = curl_exec($req);

        //Tag for response

        if ($requestType == 'bill'){
            $datatag = "gepgBillSubReqAck";
            $sigtag = "gepgSignature";
            $bookingId = $bookingId;
            $reconRequestId = null;
            $ackData = '<gepgBillSubRespAck/>';
        }elseif ($requestType == 'recon'){
            $datatag = "gepgReconSubReqAck";
            $sigtag = "gepgSignature";
            $bookingId = null;
            $reconRequestId = $reconRequestId;
            $ackData = '<gepgSpReconcRespAck/>';
        }

        if (!empty($requestResponse)){

            $vdata = self::getDataString($requestResponse,$datatag);
            $vsignature = self::getSignatureString($requestResponse,$sigtag);

            //save the xml response into the database for references
            $response = GepgBillResponse::saveGepgBillResponse($bookingId,$requestResponse,$vdata,$vsignature,$requestType,$reconRequestId);
            $billResponseId = $response->id;

            /*============= acknowledge to GePG that the request is Okay===============*/
            $response_array = array('7101'=>'trxStsCode');
            $xml = new \SimpleXMLElement($ackData);
            array_walk_recursive($response_array, array ($xml, 'addChild'));
            $xmldata =  explode("\n",$xml->asXML(),2)[1];
            echo $xmldata;

            /*==============end ack to GePG==============================*/

            curl_close($req);

            $pubData = self::readPublicKeyStore($vdata,$vsignature);
            $status = $pubData->getData()->status;
            $message = $pubData->getData()->message;

            //update response table
            GepgBillResponse::updateGepgBillResponse($billResponseId,$status,$message);

            //return $xmldata;

        }else{

            /*============= acknowledge to GePG that the request is Okay===============*/
            $response_array = array('7101'=>'trxStsCode');
            $xml = new \SimpleXMLElement($ackData);
            array_walk_recursive($response_array, array ($xml, 'addChild'));
            $xmldata =  explode("\n",$xml->asXML(),2)[1];
            echo $xmldata;

            /*==============end ack to GePG==============================*/

            //return $xmldata;

        }




    }
}
