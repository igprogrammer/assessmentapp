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



    public static function businessLicenceFeeCalculator($applyFeeinUsd,$categoryId,$sharePercntForeign,$sharePercntLocal,$isPerUnitFeeApplicable,
                                                        $branchLicenseFeeUsd,$perUnitlicenseFeeUSD,$noOfUnits,$branchLicenseFeeTShs,$perUnitlicenseFeeTShs,$expireDate=null)
    {

        $ExpireDate = $expireDate;
        $days = 21;
        $current_date = date('Y-m-d');

        $Expire_year = date('Y', strtotime($ExpireDate));
        $Expire_month = date('m', strtotime($ExpireDate));
        $Expire_day = date('d', strtotime($ExpireDate));
        $current_month = date('m', strtotime($current_date));
        $current_year = date('Y', strtotime($current_date));

        $ExDate = new \DateTime($ExpireDate);
        $curDate = new \DateTime($current_date);

        $diff = $curDate->diff($ExDate);
        $year_difference = $diff->y;
        $ydiff = $diff->y;
        $yr_diff = $diff->y;
        $month_difference = $diff->m;
        $days_difference = $diff->d;

        $currentPayableLicenceAmount = 0;
        $currentPayableLicenceAmountWithPenalty = 0;
        $penaltyAmount = 0;


        if ($year_difference >= 1){


            $fee_amount = 0;
            //check if to grant grace period to the current year
            if ((int)$Expire_month <= (int)$current_month){//if expire date is less than or equal to the current month

                for ($year_difference; ((int)$current_year - (int)$year_difference) <= (int)$current_year; $year_difference--){

                    if ((int)$current_year == ((int)$current_year - $year_difference)) {//current year

                        //check if to add grace period

                        $calculation_year = ($current_year - $year_difference);
                        //allow grace period
                        $ExpireD = date("Y-m-d", strtotime($expireDate . '+ ' . $days . ' days'));
                        $ExpiryMonth = date('m',strtotime($ExpireD));
                        $ExpiryDay = \date('d',strtotime($ExpireD));

                        $calculation_date = $calculation_year.'-'.$ExpiryMonth.'-'.$ExpiryDay;
                        $calculation_date = date('Y-m-d',strtotime($calculation_date));


                        $today_date = date('Y-m-d');

                        if ($calculation_date < $today_date){


                            $calculation_date = new \DateTime($ExpireD);
                            $diff = $calculation_date->diff($curDate);
                            $difference_in_years = $diff->y;
                            $difference_months = $diff->m;
                            $difference_days = $diff->d;


                            if ($difference_in_years > 0){
                                $years_in_months = $difference_in_years * 12;
                                if ($difference_months > 0){
                                    $difference_days = ($difference_months * 30) + $difference_days;
                                }else{
                                    $difference_days = $difference_days;
                                }
                            }else{
                                $years_in_months = 0;
                                if ($difference_months > 0){
                                    $difference_days = ($difference_months * 30) + $difference_days;
                                }else{
                                    $difference_days = $difference_days;
                                }
                            }


                            if ($difference_days >= 30){

                                $number_of_days = (int)fmod($difference_days,30);
                                if ($number_of_days > 0){
                                    $number_of_months = 1;
                                    $months = $difference_months + $number_of_months;
                                }elseif ($number_of_days == 0){
                                    $months = $difference_days/30;
                                }else{
                                    $months = $difference_months;
                                }

                            }else{
                                $months = 1;
                            }

                        }else{
                            $months = 0;
                        }


                        //call function to get penalty percentage by passing number of months elapsed
                        //$penaltyPercentage =GeneralController::getPenaltyPercentage($months);
                        $months = $months + $years_in_months;
                        $months = $months;
                        $penaltyPercentage = self::getPenaltyPercentage($months);
                        $data = self::getBill($applyFeeinUsd,$categoryId,$sharePercntForeign,$sharePercntLocal,$isPerUnitFeeApplicable,
                            $branchLicenseFeeUsd,$perUnitlicenseFeeUSD,$noOfUnits,$branchLicenseFeeTShs,$perUnitlicenseFeeTShs,$expireDate);

                        $billAmount = $data->getData()->billAmount;
                        $amountWithPenalty = $data->getData()->amountWithPenalty;
                        $result = $data->getData()->result;

                        $currentPayableLicenceAmount = $currentPayableLicenceAmount + ($billAmount + ($billAmount * $penaltyPercentage));
                        $currentPayableLicenceAmountWithPenalty =$currentPayableLicenceAmountWithPenalty + ($amountWithPenalty + ($amountWithPenalty * $penaltyPercentage));
                        $penaltyAmount = $penaltyAmount + ($amountWithPenalty * $penaltyPercentage);


                    }
                    else{//not current year


                        $calculation_year = ($current_year - $year_difference);
                        //allow grace period
                        $ExpireD = date("Y-m-d", strtotime($ExpireDate . '+ ' . $days . ' days'));
                        $ExpiryMonth = date('m',strtotime($ExpireD));
                        $ExpiryDay = \date('d',strtotime($ExpireD));

                        $calculation_date = $calculation_year.'-'.$ExpiryMonth.'-'.$ExpiryDay;
                        $calculation_date = date('Y-m-d',strtotime($calculation_date));

                        $today_date = date('Y-m-d');
                        $months = 0;
                        if ($calculation_date < $today_date){


                            $calculation_date = new \DateTime($calculation_date);
                            $diff = $calculation_date->diff($curDate);
                            $difference_in_years = $diff->y;
                            $difference_months = $diff->m;
                            $difference_days = $diff->d;



                            if ($difference_in_years > 0){
                                $months = $difference_in_years * 12;
                            }

                            if ($difference_days >= 30){

                                $number_of_days = (int)fmod($difference_days,30);
                                if ($number_of_days > 0){
                                    //$number_of_months = 1;//for cm
                                    $number_of_months = 0;
                                    $months = $months + ($difference_months + $number_of_months);
                                }elseif ($number_of_days == 0){
                                    if ($difference_months > 0){
                                        $months = $months + $difference_months;
                                    }else{
                                        $months = $months + $difference_days/30;
                                    }

                                }else{
                                    $months = $months + $difference_months;
                                }

                            }
                            else{

                                if ($difference_months > 0){
                                    $months = $months + $difference_months;
                                }else{
                                    $months = $months + 0;
                                }

                            }


                        }

                        $months = $months;
                        $penaltyPercentage = self::getPenaltyPercentage($months);
                        $data = self::getBill($applyFeeinUsd,$categoryId,$sharePercntForeign,$sharePercntLocal,$isPerUnitFeeApplicable,
                            $branchLicenseFeeUsd,$perUnitlicenseFeeUSD,$noOfUnits,$branchLicenseFeeTShs,$perUnitlicenseFeeTShs,$expireDate);

                        $billAmount = $data->getData()->billAmount;
                        $amountWithPenalty = $data->getData()->amountWithPenalty;
                        $result = $data->getData()->result;

                        $currentPayableLicenceAmount = $currentPayableLicenceAmount + ($billAmount + ($billAmount * $penaltyPercentage));
                        $currentPayableLicenceAmountWithPenalty = $currentPayableLicenceAmountWithPenalty + ($amountWithPenalty + ($amountWithPenalty * $penaltyPercentage));
                        $penaltyAmount = $penaltyAmount + ($amountWithPenalty * $penaltyPercentage);

                    }


                }
            }
        }
        else{


            //just calculate the number of months after the grace period
            $calculation_year = date('Y',strtotime($expireDate));

            $calculation_date = $calculation_year.'-'.$Expire_month.'-'.$Expire_day;
            $calculation_date = date("Y-m-d", strtotime($calculation_date . '+ ' . $days . ' days'));

            $calculation_date = date('Y-m-d',strtotime($calculation_date));


            $current_date = new \DateTime($current_date);



            $calculation_date = new \DateTime($calculation_date);
            $diff = $current_date->diff($calculation_date);
            $difference_in_years = $diff->y;
            $difference_months = $diff->m;
            $difference_days = $diff->d;


            if ($difference_in_years > 0){
                $years_in_months = $difference_in_years * 12;
                if ($difference_months > 0){
                    $difference_days = ($difference_months * 30) + $difference_days;
                }else{
                    $difference_days = $difference_days;
                }
            }else{
                $years_in_months = 0;
                if ($difference_months > 0){
                    $difference_days = ($difference_months * 30) + $difference_days;
                }else{
                    $difference_days = $difference_days;
                }
            }


            if ($difference_days > 30){

                $number_of_days = (int)fmod($difference_days,30);
                if ($number_of_days > 0){
                    $number_of_months = 1;
                    $months = $difference_months + $number_of_months;
                }elseif ($number_of_days == 0){
                    $months = $difference_days/30;
                }else{
                    $months = $difference_months;
                }

            }else{
                $calculation_date = $calculation_year.'-'.$Expire_month.'-'.$Expire_day;
                $calculation_date = date("Y-m-d", strtotime($calculation_date . '+ ' . $days . ' days'));
                $c_date = \date('Y-m-d');
                if ($calculation_date <= $c_date){
                    $months = 1;
                }else{
                    $months = 0;
                }
            }

            $months = $months + $years_in_months;
            //return $months;
            $months = $months;

            $penaltyPercentage = self::getPenaltyPercentage($months);
            $data = self::getBill($applyFeeinUsd,$categoryId,$sharePercntForeign,$sharePercntLocal,$isPerUnitFeeApplicable,
                $branchLicenseFeeUsd,$perUnitlicenseFeeUSD,$noOfUnits,$branchLicenseFeeTShs,$perUnitlicenseFeeTShs,$expireDate);

            $billAmount = $data->getData()->billAmount;
            $amountWithPenalty = $data->getData()->amountWithPenalty;
            $result = $data->getData()->result;

            $currentPayableLicenceAmount = $currentPayableLicenceAmount + ($billAmount + ($billAmount * $penaltyPercentage));
            $currentPayableLicenceAmountWithPenalty = $currentPayableLicenceAmountWithPenalty + ($amountWithPenalty + ($amountWithPenalty * $penaltyPercentage));
            $penaltyAmount = $penaltyAmount + ($amountWithPenalty * $penaltyPercentage);

        }

        return response()->json(['billAmount'=>(int)$currentPayableLicenceAmount,'amountWithPenalty'=>(int)$currentPayableLicenceAmountWithPenalty,'penaltyAmount'=>(int)$penaltyAmount]);

    }


    public static function getBill($applyFeeinUsd,$categoryId,$sharePercntForeign,$sharePercntLocal,$isPerUnitFeeApplicable,
                                   $branchLicenseFeeUsd,$perUnitlicenseFeeUSD,$noOfUnits,$branchLicenseFeeTShs,$perUnitlicenseFeeTShs,$expireDate=null){
        if ($applyFeeinUsd == 1){

            if ($categoryId == 28){
                if ($sharePercntForeign > $sharePercntLocal){
                    if ($isPerUnitFeeApplicable == 1){
                        $billAmount = $branchLicenseFeeUsd + ($perUnitlicenseFeeUSD * $noOfUnits);
                    }else{
                        $billAmount = $branchLicenseFeeUsd;
                    }

                    $amountWithPenalty = $billAmount * exRate();

                }else{

                    if ($isPerUnitFeeApplicable == 1){
                        $billAmount = $branchLicenseFeeTShs + ($perUnitlicenseFeeTShs * $noOfUnits);
                    }else{
                        $billAmount = $branchLicenseFeeTShs;
                    }

                    $amountWithPenalty = $billAmount;

                }
            }else{

                if ($sharePercntForeign > $sharePercntLocal){
                    if ($isPerUnitFeeApplicable == 1){
                        $billAmount = $branchLicenseFeeUsd + ($perUnitlicenseFeeUSD * $noOfUnits);
                    }else{
                        $billAmount = $branchLicenseFeeUsd;
                    }

                    $amountWithPenalty = $billAmount * exRate();

                }else{

                    if ($isPerUnitFeeApplicable == 1){
                        $billAmount = $branchLicenseFeeTShs + ($perUnitlicenseFeeTShs * $noOfUnits);
                    }else{
                        $billAmount = $branchLicenseFeeTShs;
                    }

                    $amountWithPenalty = $billAmount;

                }





                /*if ($isPerUnitFeeApplicable == 1){
                    $billAmount = $branchLicenseFeeUsd + ($perUnitlicenseFeeUSD * $noOfUnits);
                }else{
                    $billAmount = $branchLicenseFeeUsd;
                }

                $amountWithPenalty = $billAmount * exRate();*/
            }

        }
        else{


            if ($isPerUnitFeeApplicable == 1){
                $billAmount = $branchLicenseFeeTShs + ($perUnitlicenseFeeTShs * $noOfUnits);
            }else{
                $billAmount = $branchLicenseFeeTShs;
            }

            $amountWithPenalty = $billAmount;



            /*if ($sharePercntForeign > $sharePercntLocal){

                if ($isPerUnitFeeApplicable == 1){
                    $billAmount = $branchLicenseFeeUsd + ($perUnitlicenseFeeUSD * $noOfUnits);
                }else{
                    $billAmount = $branchLicenseFeeUsd;
                }

                $amountWithPenalty = $billAmount * exRate();

            }else{

                if ($isPerUnitFeeApplicable == 1){
                    $billAmount = $branchLicenseFeeTShs + ($perUnitlicenseFeeTShs * $noOfUnits);
                }else{
                    $billAmount = $branchLicenseFeeTShs;
                }

                $amountWithPenalty = $billAmount;
            }*/

        }

        if ($billAmount > 0 && $amountWithPenalty > 0){
            $result = 1;
        }else{
            $result = 0;
        }

        return response()->json(['result'=>$result,'billAmount'=>$billAmount,'amountWithPenalty'=>$amountWithPenalty]);


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
