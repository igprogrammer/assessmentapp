<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FeeAccount extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public static function updateFeeAccount($feeAccountId,$division_id,$account_code,$account_name,$group_number){
        $fee_account = FeeAccount::find($feeAccountId);
        $fee_account->division_id = $division_id;
        $fee_account->account_code = $account_code;
        $fee_account->account_name = $account_name;
        $fee_account->group_number = $group_number;
        $fee_account->save();

        return $fee_account;
    }

    public static function saveFeeAccount($division_id,$account_code,$account_name,$group_number){
        $fee_account = new FeeAccount();
        $fee_account->division_id = $division_id;
        $fee_account->account_code = $account_code;
        $fee_account->account_name = $account_name;
        $fee_account->group_number = $group_number;
        $fee_account->user_id = Auth::user()->id;
        $fee_account->save();
    }

    public static $add_rules = array(
        'division_id'=>'required',
        'account_code'=>'required',
        'account_name'=>'required',
        'group_number'=>'required'
    );
}
