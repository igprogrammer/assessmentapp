<?php

namespace App\Models\Payment;

use App\Models\SystemConfig\SystemConfig;
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

        $sysConfig = SystemConfig::invoiceGeneration();
        if ($sysConfig->invoiceGeneration == 1){

            if (Auth::user()->isSupervisor == 1){
                //return TempPayment::whereIn('status', array(0,2))->paginate();
                return TempPayment::whereIn('status', array(2))->orderBy('id','DESC')->paginate();
            }elseif (Auth::user()->isSupervisor == 2){
                return TempPayment::whereIn('status', array(3))->orderBy('id','DESC')->paginate();
            }else{
                return TempPayment::where(['user_id'=>Auth::user()->id,'status'=>0])->orderBy('id','DESC')->paginate();
            }

        }else{
            return TempPayment::where(['user_id'=>Auth::user()->id,'status'=>0])->orderBy('id','DESC')->paginate();
        }

    }

    public static function updateTempStatus($tempId,$status){
        return DB::table('temp_payments')->where(['id'=>$tempId])->update(array('status'=>$status));
    }
}
