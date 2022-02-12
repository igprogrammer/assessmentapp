<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaymentFee extends Model
{
    use HasFactory;

    public static function getAssessmentItems($paymentId){
        return DB::connection('sqlsrv')->table('payments as p')
            ->join('payment_fees as pf','pf.payment_id','=','p.id')
            ->join('fee_items as fi','fi.id','pf.fee_item_id')
            ->where(['payment_id'=>$paymentId])->paginate();
    }
}
