<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GepgCode extends Model
{
    use HasFactory;

    public static function saveCodeResponse($bookingId,$PayCntrNum,$TrxSts,$TrxStsCode){
        $data = new GepgCode();
        $data->BillId = $bookingId;
        $data->PayCntrNum = $PayCntrNum;
        $data->TrxSts = $TrxSts;
        $data->TrxStsCode = $TrxStsCode;
        $data->save();
    }
}
