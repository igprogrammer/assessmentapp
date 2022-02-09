<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ErrorsLog extends Model
{
    use HasFactory;

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
