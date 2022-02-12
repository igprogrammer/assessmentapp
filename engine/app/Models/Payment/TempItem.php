<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempItem extends Model
{
    use HasFactory;

    public static function getTempItems($temp_payment_id){
        return TempItem::where(['temp_payment_id'=>$temp_payment_id])->get();
    }
}
