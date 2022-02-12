<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentAttachment extends Model
{
    use HasFactory;

    public static function getAssessmentAttachments($payment_id){
        return AssessmentAttachment::where(['payment_id'=>$payment_id])->get();
    }
}
