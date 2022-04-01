<?php

namespace App\Http\Controllers\Assessment;

use App\Http\Controllers\Controller;
use App\Models\Assessment\ErrorsLog;
use App\Models\SystemConfig\SystemConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeneralController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public static function invoiceGeneration(){
        return SystemConfig::invoiceGeneration();
    }

    //get penalty percentage
    public static function getPenaltyPercentage($months){
        $currentYearPenaltyMonths = array();
        if ($months > 0){
            for ($i=0;$months>$i;$i++){
                $currentYearPenaltyMonths[] = ($i + 1);
            }
        }

        //Get PENALTY PERCENTAGE
        $counter = 0;
        $penaltyPercentage = 0;
        if (!empty($currentYearPenaltyMonths)){
            foreach ($currentYearPenaltyMonths as $currentYearPenaltyMonth){

                if (!empty($currentYearPenaltyMonths[$counter] && $currentYearPenaltyMonths[$counter] == 1)){
                    $penaltyPercentage = 0.25;
                }elseif (!empty($currentYearPenaltyMonths[$counter] && $currentYearPenaltyMonths[$counter] == 2)){
                    $penaltyPercentage = 0.27;
                }elseif (!empty($currentYearPenaltyMonths[$counter] && $currentYearPenaltyMonths[$counter] == 3)){
                    $penaltyPercentage = 0.29;
                }elseif (!empty($currentYearPenaltyMonths[$counter] && $currentYearPenaltyMonths[$counter] == 4)){
                    $penaltyPercentage = 0.31;
                }elseif (!empty($currentYearPenaltyMonths[$counter] && $currentYearPenaltyMonths[$counter] == 5)){
                    $penaltyPercentage = 0.33;
                }elseif (!empty($currentYearPenaltyMonths[$counter] && $currentYearPenaltyMonths[$counter] == 6)){
                    $penaltyPercentage = 0.35;
                }elseif (!empty($currentYearPenaltyMonths[$counter] && $currentYearPenaltyMonths[$counter] == 7)){
                    $penaltyPercentage = 0.37;
                }elseif (!empty($currentYearPenaltyMonths[$counter] && $currentYearPenaltyMonths[$counter] == 8)){
                    $penaltyPercentage = 0.39;
                }elseif (!empty($currentYearPenaltyMonths[$counter] && $currentYearPenaltyMonths[$counter] == 9)){
                    $penaltyPercentage = 0.41;
                }elseif (!empty($currentYearPenaltyMonths[$counter] && $currentYearPenaltyMonths[$counter] == 10)){
                    $penaltyPercentage = 0.43;
                }elseif (!empty($currentYearPenaltyMonths[$counter] && $currentYearPenaltyMonths[$counter] == 11)){
                    $penaltyPercentage = 0.45;
                }elseif (!empty($currentYearPenaltyMonths[$counter] && $currentYearPenaltyMonths[$counter] == 12) || $currentYearPenaltyMonths[$counter] > 12){
                    $penaltyPercentage = 0.47;
                }else{
                    $penaltyPercentage = 0;
                }

                $counter++;
            }
        }

        return $penaltyPercentage;


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
