<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempPayment extends Model
{
    use HasFactory;

    public static function getTempPaymentInfo($id){
        return TempPayment::where('id','=',$id)->where('status','=','0')->first();
    }

    public static function getPendingAssessments(){
        return TempPayment::where(['status'=>0])->paginate();
    }
}
