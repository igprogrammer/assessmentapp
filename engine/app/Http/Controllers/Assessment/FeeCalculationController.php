<?php

namespace App\Http\Controllers\Assessment;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Fee;
use App\Models\Assessment\FeeItem;
use Illuminate\Http\Request;

class FeeCalculationController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function calculateFee(Request $request){
        try {

            $item_id = $request->item_id;
            $division_id = $request->division_id;
            $fee_account_id = $request->fee_account_id;
            $filing_date = $request->filing_date;
            $year = $request->year;
            $fee_id = $request->fee_id;
            $number_of_files = $request->number_of_files;
            $current_date = date('Y-m-d');

            if (!empty($number_of_files || $number_of_files != null)){
                $number_of_files = $number_of_files;
            }else{
                $number_of_files = '1';
            }

            $item = FeeItem::find($item_id);
            $fee_id = $fee_id ?? $item->fee_id;

            $total_amount = 0;//initialize total amount for each assessment item

            if (!empty($item_id)){

                $fee_item = FeeItem::find($item_id);//get fee item details
                $fee = Fee::find($fee_item->fee_id);//get fee details
                $has_form = $fee->has_form;
                $fee_name = $fee->fee_name;

                if (!empty($fee)){

                    $account_code = $fee->account_code;
                    $item_amount = $fee_item->item_amount;
                    $penalty = $fee_item->penalty_amount;
                    $currency = $fee_item->currency;
                    $days = $fee_item->days;
                    $copy_charges = $fee_item->copy_charge;

                    if ($has_form == 'yes'){
                        $item_name = $fee_item->item_name;
                    }else{
                        $fee = Fee::find($fee_item->fee_id);
                        $item_name = $fee->fee_name;
                    }


                    if ($account_code == 440331){

                        if (in_array($fee_id, array(18,19,20,21,69,70))){

                            if ($has_form == 'yes'){//it has forms


                                $current_month = date('m');

                                $current_date = date('Y-m-d');
                                $curr_date = new \DateTime($current_date);
                                $current_year = date('Y');
                                $date_of_filing = new \DateTime($filing_date);
                                $filing_month = \date('m',strtotime($filing_date));//filing month
                                $current_month_and_year = \date('Y-m',strtotime($current_date));//current month
                                $filing_month_and_year = \date('Y-m',strtotime($filing_date));
                                $filing_year = \date('Y',strtotime($filing_date));//filing year


                                $diff = $curr_date->diff($date_of_filing);
                                $year_difference= $diff->y;
                                $ydiff = $diff->y;
                                $yr_diff = $diff->y;
                                $month_difference=$diff->m;
                                $day_differences=$diff->d;

                                //check if the filing date is greater or less than current date
                                if ($year_difference > 1){
                                    $months = $year_difference*12;
                                }else{
                                    $months = $month_difference;
                                }

                                $filing_year = date('Y',strtotime($filing_date));
                                $filing_month = date('m',strtotime($filing_date));
                                $filing_day = date('d',strtotime($filing_date));
                                //check if year differences is greater than one

                                if ($year_difference >= 1){

                                    $initial_amount = 0;
                                    $fee_amount = 0;

                                    //check if to grant grace period to the current year
                                    if ($filing_month <= $current_month){// filing month less than or equal to the current month

                                        for ($year_difference; ($current_year - $year_difference)<=$current_year; $year_difference--){
                                            if ($current_year == ($current_year - $year_difference)) {

                                                //check if the filing month is less than or equal to the current month
                                                //grant grace period
                                                //check if to add grace period
                                                $calculation_year = ($current_year - $year_difference);
                                                //allow grace period
                                                $file_date = date("Y-m-d", strtotime($filing_date . '+ ' . $days . ' days'));
                                                $file_month = date('m',strtotime($file_date));
                                                $filing_day = \date('d',strtotime($file_date));


                                                $calculation_date = $calculation_year.'-'.$file_month.'-'.$filing_day;
                                                $calculation_date = date('Y-m-d',strtotime($calculation_date));

                                                $today_date = date('Y-m-d');

                                                if ($calculation_date < $today_date){

                                                    $calculation_date = new \DateTime($calculation_date);
                                                    $diff = $calculation_date->diff($curr_date);
                                                    $difference_in_years = $diff->y;
                                                    $difference_months = $diff->m;
                                                    $difference_days = $diff->d;

                                                    if ($difference_months > 0){
                                                        $difference_days = ($difference_months * 30) + $difference_days;
                                                    }else{
                                                        $difference_days = $difference_days;
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
                                                        $months = 1;
                                                    }


                                                }else{//calculation greater than today date
                                                    $months = 0;
                                                }



                                                $fee_amount = $months * $penalty;
                                                $initial_amount = $fee_amount + $item_amount;

                                            }
                                        }

                                    }else{//filing month is greater than current month,don't give any grace period

                                        //grant grace period to the year less to the current year
                                        for ($yr_diff; ($current_year - $yr_diff)<=$current_year; $yr_diff--){
                                            if ($current_year == ($current_year - $yr_diff)) {

                                            }else{

                                                //grant grace period to the year before the current year
                                                if (($current_year - 1) == ($current_year - $yr_diff)){

                                                    //check if to add grace period
                                                    $calculation_year = ($current_year - $yr_diff);

                                                    //allow grace period
                                                    $file_month = date('m',strtotime($filing_date));


                                                    $file_date = date("Y-m-d", strtotime($filing_date . '+ ' . $days . ' days'));
                                                    $calculation_date = $calculation_year.'-'.$file_month.'-'.$filing_day;

                                                    $calculation_date = date("Y-m-d", strtotime($calculation_date . '+ ' . $days . ' days'));

                                                    $today_date = date('Y-m-d');

                                                    if ($calculation_date < $today_date){

                                                        $calculation_date = new \DateTime($calculation_date);
                                                        $diff = $curr_date->diff($calculation_date);
                                                        $difference_in_years = $diff->y;
                                                        $difference_months = $diff->m;
                                                        $difference_days = $diff->d;

                                                        if ($difference_months > 0){
                                                            $difference_days = ($difference_months * 30) + $difference_days;
                                                        }else{
                                                            $difference_days = $difference_days;
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
                                                            $months = 1;
                                                        }


                                                    }else{//calculation greater than today date
                                                        $months = 0;
                                                    }

                                                    $fee_amount = $months * $penalty;
                                                    $initial_amount = $fee_amount + $item_amount;

                                                }


                                            }


                                        }



                                    }


                                    $total_amt = 0;

                                    for ($ydiff; ($current_year - $ydiff)<=$current_year; $ydiff--){
                                        if ($current_year == ($current_year - $ydiff)){



                                        }else{

                                            //decide which year to go back
                                            if ($filing_month <= $current_month){
                                                $calculation_year = ($current_year - $ydiff);

                                                //$calculation_year = ($current_year - $ydiff);
                                                $calculation_date = $calculation_year.'-'.$filing_month.'-'.$filing_day;
                                                $calculation_date = date('Y-m-d',strtotime($calculation_date));


                                                $current_filing_date = $current_year.'-'.$filing_month.'-'.$filing_day;;
                                                $current_filing_date = new \DateTime($current_filing_date);

                                                $calculation_date = new \DateTime($calculation_date);
                                                $diff = $current_filing_date->diff($calculation_date);
                                                $difference_in_years = $diff->y;
                                                $difference_months = $diff->m;
                                                $difference_days = $diff->d;


                                                if ($difference_in_years >= 1 && $difference_months > 0){
                                                    $months = ($difference_in_years * 12) + $difference_months;
                                                }elseif ($difference_in_years >= 1 && $difference_months <= 0){
                                                    $months = ($difference_in_years * 12) + $difference_months;
                                                }else{
                                                    $months = $difference_months;
                                                }

                                                $fee_amount = $months * $penalty;
                                                $amt = $fee_amount + $initial_amount;
                                                $total_amt = $total_amt + $amt;



                                            }else{//filing month greater than current month of the current year
                                                $calculation_year = ($current_year - ($ydiff + 1));

                                                if (($current_year - 1) == ($current_year - ($ydiff + 1))){

                                                }else{

                                                    //$calculation_year = ($current_year - $ydiff);
                                                    $calculation_date = $calculation_year.'-'.$filing_month.'-'.$filing_day;
                                                    $calculation_date = date('Y-m-d',strtotime($calculation_date));

                                                    $current_filing_date = $current_year.'-'.$filing_month.'-'.$filing_day;
                                                    $current_filing_date = new \DateTime($current_filing_date);

                                                    $demarkation_year = $current_year - 1;

                                                    $c_date =  new \DateTime($demarkation_year.'-'.$filing_month.'-'.$filing_day);//assumed current date for the filing month greater then current month


                                                    $calculation_date = new \DateTime($calculation_date);
                                                    $diff = $c_date->diff($calculation_date);
                                                    $difference_in_years = $diff->y;
                                                    $difference_months = $diff->m;
                                                    $difference_days = $diff->d;


                                                    if ($difference_in_years >= 1 && $difference_months > 0){
                                                        $months = ($difference_in_years * 12) + $difference_months;
                                                    }elseif ($difference_in_years >= 1 && $difference_months <= 0){
                                                        $months = ($difference_in_years * 12) + $difference_months;
                                                    }else{
                                                        $months = $difference_months;
                                                    }

                                                    $fee_amount = $months * $penalty;
                                                    $amt = $fee_amount + $initial_amount;
                                                    $total_amt = $total_amt + $amt;
                                                }


                                            }


                                        }
                                        $total_amount = $total_amt + $initial_amount;
                                        $total_amount;

                                    }

                                    return response()->json(['has_form'=>$has_form, 'item_name'=>$item_name, 'item_amount'=>$total_amount,
                                        'penalty_amount'=>$penalty, 'currency'=>$currency, 'days'=>$days, 'copy_charge'=>$copy_charges, 'success'=>'1',
                                        'number_of_files'=>$number_of_files]);


                                }
                                else{//year differences is less than one


                                    //just calculate the number of months after the grace period
                                    $calculation_year = $filing_year;

                                    $calculation_date = $calculation_year.'-'.$filing_month.'-'.$filing_day;
                                    $calculation_date = date("Y-m-d", strtotime($calculation_date . '+ ' . $days . ' days'));

                                    $calculation_date = date('Y-m-d',strtotime($calculation_date));

                                    //if the calculation date is greater than current date
                                    if ($calculation_date >= $current_date){
                                        $months = 0;
                                        $years_in_months = 0;
                                    }else{

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
                                            $calculation_date = $calculation_year.'-'.$filing_month.'-'.$filing_day;
                                            $calculation_date = date("Y-m-d", strtotime($calculation_date . '+ ' . $days . ' days'));
                                            $c_date = \date('Y-m-d');
                                            if ($calculation_date <= $c_date){
                                                $months = 1;
                                            }else{
                                                $months = 0;
                                            }

                                        }


                                    }


                                    $months = $months + $years_in_months;



                                    $fee_amount = ($months * $penalty) + $item_amount;

                                    $total_amount = $total_amount + $fee_amount;

                                    return response()->json(['has_form'=>$has_form, 'item_name'=>$item_name, 'item_amount'=>$total_amount,
                                        'penalty_amount'=>$penalty, 'currency'=>$currency, 'days'=>$days, 'copy_charge'=>$copy_charges, 'success'=>'1',
                                        'number_of_files'=>$number_of_files]);

                                }

                            }
                            else{// filing but no forms
                                $total_amount = $item_amount;
                                $penalty = $penalty;
                                $currency = $currency;
                                $days = $days;
                                $copy_charges = $copy_charges;
                                return response()->json(['has_form'=>$has_form, 'item_name'=>$item_name, 'item_amount'=>$total_amount,
                                    'penalty_amount'=>$penalty, 'currency'=>$currency,
                                    'days'=>$days, 'copy_charge'=>$copy_charges, 'success'=>'1', 'number_of_files'=>$number_of_files]);
                            }

                        }
                        else{

                            if (in_array($fee->id, array(61,52,53,10,11,14,30,42,43,51))){
                                $total_amount = $item_amount;
                                $penalty = $penalty;
                                $currency = $currency;
                                $days = $days;
                                $copy_charges = $copy_charges;
                            }
                            elseif (in_array($fee->id, array(25,26,27))){

                                //Start late filing

                                $current_date = date('Y-m-d');
                                $curr_date = new \DateTime($current_date);
                                $current_year = date('Y');
                                $date_of_filing = new \DateTime($filing_date);
                                $filing_month = \date('m',strtotime($filing_date));//filing month
                                $current_month_and_year = \date('Y-m',strtotime($current_date));//current month
                                $filing_month_and_year = \date('Y-m',strtotime($filing_date));
                                $filing_year = \date('Y',strtotime($filing_date));//filing year
                                $current_month = date('m');


                                $diff = $curr_date->diff($date_of_filing);
                                $year_difference= $diff->y;
                                $ydiff = $diff->y;
                                $yr_diff = $diff->y;
                                $month_difference=$diff->m;
                                $day_differences=$diff->d;

                                //check if the filing date is greater or less than current date
                                if ($year_difference > 1){
                                    $months = $year_difference*12;
                                }else{
                                    $months = $month_difference;
                                }

                                $filing_year = date('Y',strtotime($filing_date));
                                $filing_month = date('m',strtotime($filing_date));
                                $filing_day = date('d',strtotime($filing_date));
                                //check if year differences is greater than one

                                if ($year_difference >= 1){

                                    $initial_amount = 0;
                                    $fee_amount = 0;

                                    //check if to grant grace period to the current year
                                    if ($filing_month <= $current_month){// filing month less than or equal to the current month

                                        for ($year_difference; ($current_year - $year_difference)<=$current_year; $year_difference--){
                                            if ($current_year == ($current_year - $year_difference)) {

                                                //check if the filing month is less than or equal to the current month
                                                //grant grace period
                                                //check if to add grace period
                                                $calculation_year = ($current_year - $year_difference);
                                                //allow grace period
                                                $file_date = date("Y-m-d", strtotime($filing_date . '+ ' . $days . ' days'));
                                                $file_month = date('m',strtotime($file_date));
                                                $filing_day = \date('d',strtotime($file_date));


                                                $calculation_date = $calculation_year.'-'.$file_month.'-'.$filing_day;
                                                $calculation_date = date('Y-m-d',strtotime($calculation_date));


                                                $today_date = date('Y-m-d');

                                                if ($calculation_date < $today_date){

                                                    $calculation_date = new \DateTime($file_date);
                                                    $diff = $calculation_date->diff($curr_date);
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
                                                        $months = 1;
                                                    }


                                                }else{//calculation greater than today date
                                                    $months = 0;
                                                }

                                                //get total months
                                                $months = $months + $years_in_months;


                                                $fee_amount = $months * $penalty;

                                            }


                                        }

                                    }else{//filing month is greater than current month,don't give any grace period
                                        //dd('The filing month is greater than current moth');



                                        //grant grace period to the year less to the current year
                                        for ($yr_diff; ($current_year - $yr_diff)<=$current_year; $yr_diff--){
                                            if ($current_year == ($current_year - $yr_diff)) {

                                            }else{

                                                //grant grace period to the year before the current year
                                                if (($current_year - 1) == ($current_year - $yr_diff)){


                                                    //check if to add grace period
                                                    $calculation_year = ($current_year - $yr_diff);

                                                    //allow grace period
                                                    $file_month = date('m',strtotime($filing_date));


                                                    $file_date = date("Y-m-d", strtotime($filing_date . '+ ' . $days . ' days'));
                                                    //dd($file_date);
                                                    $calculation_date = $calculation_year.'-'.$file_month.'-'.$filing_day;

                                                    $calculation_date = date("Y-m-d", strtotime($calculation_date . '+ ' . $days . ' days'));


                                                    $today_date = date('Y-m-d');

                                                    if ($calculation_date < $today_date){


                                                        $calculation_date = new \DateTime($calculation_date);
                                                        $diff = $curr_date->diff($calculation_date);
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
                                                            $months = 1;
                                                        }


                                                    }else{//calculation greater than today date
                                                        $months = 0;
                                                    }

                                                    //get total months
                                                    $months = $months + $years_in_months;

                                                    $fee_amount = $months * $penalty;

                                                }


                                            }


                                        }



                                    }

                                    $total_amount = $fee_amount;


                                }else{//year differences is less than one

                                    //just calculate the number of months after the grace period
                                    $calculation_year = $filing_year;

                                    $calculation_date = $calculation_year.'-'.$filing_month.'-'.$filing_day;
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
                                        $calculation_date = $calculation_year.'-'.$filing_month.'-'.$filing_day;
                                        $calculation_date = date("Y-m-d", strtotime($calculation_date . '+ ' . $days . ' days'));
                                        $c_date = \date('Y-m-d');
                                        if ($calculation_date <= $c_date){
                                            $months = 1;
                                        }else{
                                            $months = 0;
                                        }
                                    }

                                    $months = $months + $years_in_months;



                                    $fee_amount = ($months * $penalty) + $item_amount;

                                    $total_amount = $total_amount + $fee_amount;



                                    //create response
                                    return response()->json(['has_form'=>$has_form, 'item_name'=>$item_name, 'item_amount'=>$total_amount,
                                        'penalty_amount'=>$penalty, 'currency'=>$currency, 'days'=>$days, 'copy_charge'=>$copy_charges,
                                        'success'=>'1',
                                        'number_of_files'=>$number_of_files]);

                                }


                                //End late filing




                            }
                            elseif (in_array($fee->id,array(31))){//perusal
                                $total_amount = $number_of_files * $item_amount;
                                $penalty = $penalty;
                                $currency = $currency;
                                $days = $days;
                                $copy_charges = $copy_charges;
                            }
                            else{
                                return response()->json(['success'=>13,'Invalid Item code']);
                            }

                            return response()->json(['has_form'=>$has_form, 'item_name'=>$item_name, 'item_amount'=>$total_amount,
                                'penalty_amount'=>$penalty, 'currency'=>$currency, 'days'=>$days, 'copy_charge'=>$copy_charges,
                                'success'=>'1',
                                'number_of_files'=>$number_of_files]);

                        }

                    }
                    elseif ($account_code == 440332){//Business Names

                        if ($fee->id == 2){//payment of annual maintenance fee

                            $current_date = new \DateTime(date('Y-m-d'));
                            $filing_date = new \DateTime(\date('Y-m-d',strtotime($filing_date)));
                            $diff = $current_date->diff($filing_date);
                            $number_of_years = $diff->y;

                            if ($number_of_years > 0){

                                $total_amount = $number_of_years * $fee_item->item_amount;

                                $total_amount = $total_amount;
                                $penalty = $penalty;
                                $currency = $currency;
                                $days = $days;
                                $copy_charges = $copy_charges;

                            }
                        }
                        elseif (in_array($fee->id, array(9,13,41,49,60))){
                            /**
                             * Certifying Fees (Business Names)Change Fees (Business Names)Registration Fees (Business Names)Search Fees (Business Names)Cessation fees (Business Names)Perusal (Business Names)
                             */
                            $total_amount = $item_amount;
                            $penalty = $penalty;
                            $currency = $currency;
                            $days = $days;
                            $copy_charges = $copy_charges;
                        }
                        elseif ($fee->id == 66){//Perusal
                            $total_amount = $number_of_files * $item_amount;
                            $penalty = $penalty;
                            $currency = $currency;
                            $days = $days;
                            $copy_charges = $copy_charges;
                        }
                        else{
                            $total_amount = 0;
                            $penalty = 0;
                            $currency = 0;
                            $days = 0;
                            $copy_charges = 0;
                        }

                        //return response as json
                        return response()->json(['has_form'=>$has_form, 'item_name'=>$item_name, 'item_amount'=>$total_amount, 'penalty_amount'=>$penalty,
                            'currency'=>$currency, 'days'=>$days, 'copy_charge'=>$copy_charges, 'success'=>'1', 'number_of_files'=>$number_of_files]);

                    }
                    elseif ($account_code == 440341){
                        $total_amount = $item_amount;
                        $penalty = $penalty;
                        $currency = $currency;
                        $days = $days;
                        $copy_charges = $copy_charges;


                        //return response as json
                        return response()->json(['has_form'=>$has_form, 'item_name'=>$item_name, 'item_amount'=>$total_amount,'penalty_amount'=>$penalty, 'currency'=>$currency, 'days'=>$days, 'copy_charge'=>$copy_charges,
                            'success'=>'1', 'number_of_files'=>$number_of_files]);

                    }
                    elseif ($account_code == 440342){
                        if ($fee->id == 7){//business licence

                            $ExpireDate = $filing_date;
                            $days = 21;

                            $Expire_year = date('Y',strtotime($ExpireDate));
                            $Expire_month = date('m',strtotime($ExpireDate));
                            $Expire_day = date('d',strtotime($ExpireDate));
                            $current_month = date('m', strtotime($current_date));
                            $current_year = date('Y', strtotime($current_date));

                            $ExDate = new \DateTime($ExpireDate);
                            $curDate = new \DateTime($current_date);

                            $diff = $curDate->diff($ExDate);
                            $year_difference= $diff->y;
                            $ydiff = $diff->y;
                            $yr_diff = $diff->y;
                            $month_difference=$diff->m;
                            $days_difference = $diff->d;

                            if ($year_difference >= 1){

                                $currentPayableLicenceAmount = 0;
                                $fee_amount = 0;
                                //check if to grant grace period to the current year
                                if ((int)$Expire_month <= (int)$current_month){//if expire date is less than or equal to the current month

                                    for ($year_difference; ((int)$current_year - (int)$year_difference) <= (int)$current_year; $year_difference--){

                                        if ((int)$current_year == ((int)$current_year - (int)$year_difference)) {//current year
                                            //check if to add grace period
                                            $calculation_year = ($current_year - $year_difference);
                                            //allow grace period
                                            $ExpireD = date("Y-m-d", strtotime($ExpireDate . '+ ' . $days . ' days'));
                                            $ExpiryMonth = date('m',strtotime($ExpireD));
                                            $ExpiryDay = \date('d',strtotime($ExpireD));

                                            $calculation_date = $calculation_year.'-'.$ExpiryMonth.'-'.$ExpiryDay;
                                            $calculation_date = date('Y-m-d',strtotime($calculation_date));
                                            $calc_date = $calculation_date;

                                            //echo $calculation_date;
                                            //echo "<br>";


                                            $today_date = date('Y-m-d');

                                            if ($calculation_date < $today_date){


                                                $calculation_date = new \DateTime($calculation_date);
                                                $diff = $calculation_date->diff($curDate);
                                                $difference_in_years = $diff->y;
                                                $difference_months = $diff->m;
                                                $difference_days = $diff->d;

                                                if ($difference_days >= 30){

                                                    $number_of_days = (int)fmod($difference_days,30);
                                                    if ($number_of_days > 0){
                                                        //$number_of_months = 1;//for cm
                                                        $number_of_months = 0;
                                                        $months = $difference_months + $number_of_months;
                                                    }elseif ($number_of_days == 0){
                                                        if ($difference_months > 0){
                                                            $months = $difference_months;
                                                        }else{
                                                            $months = $difference_days/30;
                                                        }

                                                    }else{
                                                        $months = $difference_months;
                                                    }

                                                }else{

                                                    if ($difference_months > 0){
                                                        $months = $difference_months;
                                                    }else{
                                                        $months = 0;
                                                    }
                                                }
                                            }else{
                                                $months = 0;
                                            }


                                            //call function to get penalty percentage by passing number of months elapsed
                                            $penaltyPercentage = self::getPenaltyPercentage($months);


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

                                            $penaltyPercentage = self::getPenaltyPercentage($months);


                                        }
                                    }
                                }
                            }
                            else{

                                $months = 0;

                                if ($year_difference > 0){
                                    $months = $year_difference * 12;
                                }

                                if ($days_difference >= 30){

                                    $number_of_days = (int)fmod($days_difference,30);
                                    if ($number_of_days > 0){
                                        //$number_of_months = 1;//for cm
                                        $number_of_months = 0;
                                        $months = $months + ($month_difference + $number_of_months);
                                    }elseif ($number_of_days == 0){
                                        if ($month_difference > 0){
                                            $months = $months + $month_difference;
                                        }else{
                                            $months = $months + $days_difference/30;
                                        }

                                    }else{
                                        $months = $months + $days_difference;
                                    }

                                }
                                else{

                                    if ($month_difference > 0){
                                        $months = $months + $month_difference;
                                    }else{
                                        $months = $months;
                                    }
                                }

                                $penaltyPercentage = self::getPenaltyPercentage($months);

                            }



                        }

                        dd($fee_item);


                        //return response as json
                       return response()->json(['has_form'=>$has_form, 'item_name'=>$item_name, 'item_amount'=>$total_amount, 'penalty_amount'=>$penalty,
                           'currency'=>$currency, 'days'=>$days, 'copy_charge'=>$copy_charges, 'success'=>'1', 'number_of_files'=>$number_of_files]);

                    }
                    else{
                        return response()->json(['success'=>12]);//Invalid account code
                    }

                }else{
                    return response()->json(['success'=>11]);//no fee record was found using the reference provided
                }

            }else{
                return response()->json(['success'=>2]);
            }


        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'AssessmentController','searchAssessment','assessment-error');
            return redirect()->back()->with('error-message',$message);
        }
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

}
