<?php

namespace App\Models\Assessment;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ErrorsLog extends Model
{
    use HasFactory;

    public static function saveGeneralError($message, $bookingId = null){
        DB::table('generalErrors')->insert(array(
            'errorMessage'=>$message,
            'bookingId'=>$bookingId,
            'createdDate'=>Carbon::now('Africa/Dar_es_Salaam')
        ));
    }

    public static function saveErrorLog($type,$controller,$function,$exception,$message,$line,$trace,$messageTrace){

        $log = new ErrorsLog();
        $log->ControllerName = $controller;
        $log->FunctionName = $function;
        $log->Exception = $exception;
        $log->Trace = $trace;
        $log->ErrorMessage = $message;
        $log->CustomMessage = $messageTrace;
        $log->Line = $line;
        $log->name = Auth::user()->name;
        $log->save();

    }
}
