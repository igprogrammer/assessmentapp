<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GepgBillResponse extends Model
{
    use HasFactory;


    public static function saveGepgBillResponse($bookingId,$responseContent){
        $data = new GepgBillResponse();
        $data->billId = $bookingId;
        $data->responseContent = $responseContent;
        $data->postedOn = Carbon::now('Africa/Dar_es_Salaam');
        $data->save();
    }
}
