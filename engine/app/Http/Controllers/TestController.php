<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public static function bill(){

        //"digitalsignature\gepg.p12"
        if (!$cert_store = file_get_contents(privKey())) {
            echo "Error: Unable to read the cert file\n";
            exit;
        }
        else
        {
            if (openssl_pkcs12_read($cert_store, $cert_info, "passpass"))
            {

                //print_r($cert_info);    //Certificate Information;
                //$pkey = $cert_info['pkey'];  //private key
                //$cert = $cert_info['cert'];  //public key
                $id = round(microtime(true) * 1000);
                //Bill Request
                $content ="<gepgBillSubReq>
        <BillHdr>
            <SpCode>SP104</SpCode>
            <RtrRespFlg>true</RtrRespFlg>
        </BillHdr>
        <BillTrxInf>
            <BillId>".$id."</BillId>
            <SubSpCode>1002</SubSpCode>
            <ColCentCode>HQ</ColCentCode>
            <SpSysId>TDAWASCO001</SpSysId>
            <BillAmt>10000</BillAmt>
            <MiscAmt>0</MiscAmt>
            <BillExprDt>2021-06-27T00:00:00</BillExprDt>
            <PyrId>100109148213415390436</PyrId>
            <PyrName>Yohana</PyrName>
            <BillDesc>Sale of seedlings</BillDesc>
            <BillGenDt>2021-05-28T09:59:02</BillGenDt>
            <BillGenBy>2212</BillGenBy>
            <BillApprBy>SPPORTAL</BillApprBy>
            <PyrCellNum>0788234876</PyrCellNum>
            <PyrEmail>yohana@gmail.com</PyrEmail>
            <Ccy>TZS</Ccy>
            <BillEqvAmt>10000</BillEqvAmt>
            <RemFlag>true</RemFlag>
            <BillPayOpt>3</BillPayOpt>
            <BillItems>
                <BillItem>
                    <BillItemRef>123BN</BillItemRef>
                    <UseItemRefOnPay>N</UseItemRefOnPay>
                    <BillItemAmt>10000</BillItemAmt>
                    <BillItemEqvAmt>10000</BillItemEqvAmt>
                    <BillItemMiscAmt>0</BillItemMiscAmt>
                    <GfsCode>140202</GfsCode>
                </BillItem>
            </BillItems>
        </BillTrxInf>
    </gepgBillSubReq>";

                //create signature
                openssl_sign($content, $signature, $cert_info['pkey'], "sha1WithRSAEncryption");

                $signature = base64_encode($signature);  //output crypted data base64 encoded

                //Compose xml request
                $data = "<Gepg>".$content."<gepgSignature>".$signature."</gepgSignature></Gepg>";

                $resultCurlPost = "";
                $serverIp = "http://154.118.230.18";

                $uri = "/api/bill/sigqrequest"; //this is for qrequest
                $uri = "/api/bill/sigsrequest"; //this if sor srequest

                $data_string = $data;
                $ch = curl_init($serverIp.$uri);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type:application/xml',
                        'Gepg-Com:default.sp.in',
                        'Gepg-Code:SP104',
                        'Content-Length:'.strlen($data_string))
                );

                curl_setopt($ch, CURLOPT_TIMEOUT, 50);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 50);

                //Capture returned content from GePG
                $resultCurlPost = curl_exec($ch);
                curl_close($ch);
                //$resultCurlPost=$data;
                if(!empty($resultCurlPost)){

                    //Tag for response
                    $datatag = "gepgBillSubReqAck";
                    $sigtag = "gepgSignature";

                    $vdata = getDataString($resultCurlPost,$datatag);
                    $vsignature = getSignatureString($resultCurlPost,$sigtag);

                    //store vdata and vsignature response here

                    if (!$pcert_store = file_get_contents(pubKey())) {
                        echo "Error: Unable to read the cert file\n";
                        exit;
                    }
                    else{

                        //Read Certificate
                        if (openssl_pkcs12_read($pcert_store,$pcert_info,"passpass")) {

                            //Decode Received Signature String
                            $rawsignature = base64_decode($vsignature);

                            //Verify Signature and state whether signature is okay or not
                            $ok = openssl_verify($vdata, $rawsignature, $pcert_info['extracerts']['0']);
                            if ($ok == 1) {
                                $signatureMessageStatus = "Good";
                            } elseif ($ok == 0) {
                                $signatureMessageStatus = "Bad";
                            } else {
                                $signatureMessageStatus = "UGLY, Error checking signature";
                            }

                        }


                    }
                }
                else
                {
                    echo "No result Returned"."\n";
                }

            }
            else
            {

                echo "Error: Unable to read the cert store.\n";
                exit;
            }

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



    }
}
