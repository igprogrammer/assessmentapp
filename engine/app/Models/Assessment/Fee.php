<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Fee extends Model
{
    use HasFactory;

    //guarded
    protected $guarded = ['id'];

    public static function updateFee($feeId,$fee_account_id,$fee_name,$fee_code,$account_code,$amount,$has_form,$type,$gfs_code,$isActive){
        $fee = Fee::find($feeId);
        $fee->fee_account_id = $fee_account_id;
        $fee->fee_name = $fee_name;
        $fee->fee_code = $fee_code;
        $fee->account_code = $account_code;
        $fee->amount = $amount;
        $fee->has_form = $has_form;
        $fee->type = $type;
        $fee->gfs_code = $gfs_code;
        $fee->user_id = Auth::user()->id;
        $fee->active = $isActive;
        $fee->save();

        return $fee;
    }

    public static function saveFee($fee_account_id,$fee_name,$fee_code,$account_code,$amount,$has_form,$type,$gfs_code){
        $fee = new Fee();
        $fee->fee_account_id = $fee_account_id;
        $fee->fee_name = $fee_name;
        $fee->fee_code = $fee_code;
        $fee->account_code = $account_code;
        $fee->amount = $amount;
        $fee->has_form = $has_form;
        $fee->type = $type;
        $fee->gfs_code = $gfs_code;
        $fee->user_id = Auth::user()->id;
        $fee->save();

        return $fee;
    }

    public static $add_rules = array(
        'fee_account_id'=>'required',
        'fee_code'=>'required',
        'fee_name'=>'required',
        'account_code'=>'required',
        'type'=>'required',
        'gfs_code'=>'required',
        'has_form'=>'required',
        'amount'=>'required',
        'currency'=>'required'
    );
}
