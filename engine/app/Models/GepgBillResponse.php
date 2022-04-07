<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GepgBillResponse extends Model
{
    use HasFactory;

    public static function updateGepgBillResponse($id,$status,$message){
        DB::table('gepg_bill_responses')->where(['id'=>$id])->update(array(
            'signatureStatus'=>$status,
            'signatureMessage'=>$message
        ));
    }


    public static function saveGepgBillResponse($bookingId=null,$responseContent,$vdata=null,$vsignature=null,$requestType=null,$reconRequestId=null){
        $data = new GepgBillResponse();
        $data->billId = $bookingId;
        $data->responseContent = $responseContent;
        $data->vdata = $vdata;
        $data->vsignature = $vsignature;
        $data->requestType = $requestType;
        $data->postedOn = Carbon::now('Africa/Dar_es_Salaam');
        $data->save();

        return $data;
    }
}
