<?php

namespace App\Http\Controllers\Assessment;

use App\Http\Controllers\Controller;
use App\Models\Assessment\ErrorsLog;
use Illuminate\Support\Facades\Log;

class GeneralController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public static function exceptionHandler($type,$exception,$controller,$function,$channel = null){
        $message = $exception->getMessage().' on line '.$exception->getLine().' of file '.$exception->getFile();
        $trace = $exception->getTraceAsString();
        $line = $exception->getLine();
        $messageTrace = $controller.'@'.$function.': ['.$exception->getCode().'] "'.$exception->getMessage().'" on line '.$line.' of file '.$exception->getFile();
        Log::channel($channel)->error($message);

        ErrorsLog::saveErrorLog($type,$controller,$function,$exception,$message,$line,$trace,$messageTrace);

    }
}
