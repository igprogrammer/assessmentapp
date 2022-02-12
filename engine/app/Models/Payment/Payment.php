<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Payment extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public static function getAssessmentRecords($flag){
        if (strtolower($flag) == 'individual'){
            $records = Payment::where('payments.user_id','=',Auth::user()->id)->orderBy('payments.id','DESC')->get();
        }elseif (strtolower($flag) == 'all'){
            $records = Payment::orderBy('payments.id','DESC')->get();
        }else{
            $records = null;
        }

        return $records;

    }
}
