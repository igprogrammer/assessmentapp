<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FeeItem extends Model
{
    use HasFactory;

    public static function getFeeItems($fee_id,$ltype){
        $items = FeeItem::where(['fee_id'=>$fee_id,'active'=>'yes']);
        if (strtolower($ltype) == 'schedule a' && $ltype != null){
            $items = $items->where(['licenceType'=>$ltype]);
        }
        $items = $items->get();

        return $items;
    }

    public static function updateFeeItem($feeItemId,$fee_id,$item_name,$item_amount,$penalty_amount,$days,$copy_charge,$stamp_duty_amount,$currency,$defineFeeAmount){
        $fee_item = FeeItem::find($feeItemId);
        $fee_item->fee_id = $fee_id;
        $fee_item->item_name = $item_name;
        $fee_item->item_amount = $item_amount;
        $fee_item->penalty_amount = $penalty_amount;
        $fee_item->days = $days;
        $fee_item->copy_charge = $copy_charge;
        $fee_item->stamp_duty_amount = $stamp_duty_amount;
        $fee_item->currency = $currency;
        $fee_item->user_id = Auth::user()->id;
        $fee_item->defineFeeAmount = $defineFeeAmount;
        $fee_item->save();

        return $fee_item;
    }

    public static function saveFeeItem($fee_id,$item_name,$item_amount,$penalty_amount,$days,$copy_charge,$stamp_duty_amount,$currency,$defineFeeAmount){
        $fee_item = new FeeItem();
        $fee_item->fee_id = $fee_id;
        $fee_item->item_name = $item_name;
        $fee_item->item_amount = $item_amount;
        $fee_item->penalty_amount = $penalty_amount;
        $fee_item->days = $days;
        $fee_item->copy_charge = $copy_charge;
        $fee_item->stamp_duty_amount = $stamp_duty_amount;
        $fee_item->currency = $currency;
        $fee_item->user_id = Auth::user()->id;
        $fee_item->defineFeeAmount = $defineFeeAmount;
        $fee_item->save();

        return $fee_item;
    }

    protected $guarded = ['id'];

    public static $add_rules = array(
        'fee_account_id'=>'required',
        'item_name'=>'required',
        'item_amount'=>'required',
        'penalty_amount'=>'required',
        'days'=>'required',
        'copy_charge'=>'required',
        'stamp_duty_amount'=>'required',
        'currency'=>'required'
    );

}
