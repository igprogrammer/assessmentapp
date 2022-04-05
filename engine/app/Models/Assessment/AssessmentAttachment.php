<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentAttachment extends Model
{
    use HasFactory;

    public static function updateAssessmentAttachment($temp_payment_id,$payment_id){
        $attach = AssessmentAttachment::where(['temp_payment_id'=>$temp_payment_id])->first();
        $attach->payment_id = $payment_id;
        $attach->save();
        return $attach;
    }

    public static function checkAttachment($tempPaymentId){
        return AssessmentAttachment::where(['temp_payment_id'=>$tempPaymentId])->first();
    }

    public static function saveAttachment($temp_payment_id,$filePath,$mimeType,$file_name,$extension){
        //$path = 'report_attachments/'.$fileName;
        $attachment = new AssessmentAttachment();
        //$attachment->payment_id = $payment_id;
        $attachment->temp_payment_id = $temp_payment_id;
        //$attachment->file_path = 'assessment_attachments'.'/'.$company_number.'/'.date('YmdHis').'_'.$assessment_form_file->getClientOriginalName();
        $attachment->file_path = $filePath;
        $attachment->mime = $mimeType;
        $attachment->file_name = $file_name;
        $attachment->extension = $extension;
        $attachment->save();

        return $attachment;
    }

    public static function getAssessmentAttachments($payment_id){
        return AssessmentAttachment::where(['payment_id'=>$payment_id])->get();
    }

    public static function getAssessmentTempAttachments($payment_id){
        return AssessmentAttachment::where(['temp_payment_id'=>$payment_id])->get();
    }
}
