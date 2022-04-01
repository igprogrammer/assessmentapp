<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TempPayment extends Model
{
    use HasFactory;

    public static function getTempPaymentInfo($id){
        return TempPayment::where('id','=',$id)->whereIn('status',array(0,2))->first();
    }

    public static function getPendingAssessments(){
        if (Auth::user()->isSupervisor == 1){
            return TempPayment::whereIn('status', array(0,2))->paginate();
        }else{
            return TempPayment::where(['user_id'=>Auth::user()->id,'status'=>0])->paginate();
        }

    }

    public static function updateTempStatus($tempId,$status){
        return DB::table('temp_payments')->where(['id'=>$tempId])->update(array('status'=>$status));
    }
}
