<?php

namespace App\Http\Controllers\Assessment;

use App\Helpers\CurrencyNumberToWordConverter;
use App\Http\Controllers\Controller;
use App\Models\Assessment\AssessmentAttachment;
use App\Models\Assessment\Division;
use App\Models\Assessment\Fee;
use App\Models\Assessment\FeeAccount;
use App\Models\Assessment\FeeItem;
use App\Models\Booking\Booking;
use App\Models\Customer\Customer;
use App\Models\ExchangeRate\ExchangeRate;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentFee;
use App\Models\Payment\TempItem;
use App\Models\Payment\TempPayment;
use App\Models\User;
use Cassandra\Custom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class AssessmentController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function searchAssessment(Request $request){

        try {

            $entityName = $request->entityName;
            $entityNumber = $request->entityNumber;
            $flag = $request->flag;


            if (strtolower($flag) == 'individual'){
                if (!empty($entityName)){
                    $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                        ->join('users as u','u.id','=','p.user_id')
                        ->select('p.id as id','company_number','customer_name','currency','invoice','amount','customer_id','date_of_payment','p.user_id','name')
                        ->where('customer_name','LIKE','%'.$entityName.'%')->where(['p.user_id'=>Auth::user()->id])->get();
                }elseif (!empty($entityNumber)){
                    $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                        ->join('users as u','u.id','=','p.user_id')
                        ->select('p.id as id','company_number','customer_name','currency','invoice','amount','customer_id','date_of_payment','p.user_id','name')
                        ->where('company_number','LIKE','%'.$entityNumber.'%')->where(['p.user_id'=>Auth::user()->id])->get();
                }else{
                    $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                        ->join('users as u','u.id','=','p.user_id')
                        ->select('p.id as id','company_number','customer_name','currency','invoice','amount','customer_id','date_of_payment','p.user_id','name')
                        ->where(['p.user_id'=>Auth::user()->id])->get();
                }
            }else{
                if (!empty($entityName)){
                    $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                        ->join('users as u','u.id','=','p.user_id')
                        ->select('p.id as id','company_number','customer_name','currency','invoice','amount','customer_id','date_of_payment','p.user_id','name')
                        ->where('customer_name','LIKE','%'.$entityName.'%')->get();
                }elseif (!empty($entityNumber)){
                    $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                        ->join('users as u','u.id','=','p.user_id')
                        ->select('p.id as id','company_number','customer_name','currency','invoice','amount','customer_id','date_of_payment','p.user_id','name')
                        ->where('company_number','LIKE','%'.$entityNumber.'%')->get();
                }else{
                    $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                        ->join('users as u','u.id','=','p.user_id')
                        ->select('p.id as id','company_number','customer_name','currency','invoice','amount','customer_id','date_of_payment','p.user_id','name')
                        ->get();
                }
            }
            $flag = ucfirst($flag);

            return view('assessment.assessment.generated_assessments_filter')
                ->with('title',$flag)->with('payments',$payments)->with('flag',$flag);



        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'AssessmentController','searchAssessment','assessment-error');
            return redirect()->back()->with('error-message',$message);
        }
    }

    public function filterAssessment(Request $request){
        try {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $flag = $request->flag;

            if (strtolower($flag) == 'individual'){
                $payments = Payment::where('payments.user_id','=',Auth::user()->id)
                    ->whereBetween('add_date',array($from_date,$to_date))
                    ->orderBy('payments.id','DESC')->get();
            }else{
                $payments = Payment::whereBetween('add_date',array($from_date,$to_date))->orderBy('payments.id','DESC')->get();
            }
            $flag = ucfirst($flag);

            return view('assessments.generated_assessments')
                ->with('title',$flag)->with('payments',$payments)->with('flag',$flag);
        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'AssessmentController','filterAssessment','assessment-error');
            return redirect()->back()->with('error-message',$message);
        }
    }

    public function getFileContent($attachmentId){

        if(!empty($attachmentId)){

            $attachment = AssessmentAttachment::where('id','=',$attachmentId)->first();
            if (!empty($attachment)){
                $payment = Payment::find($attachment->payment_id);
                $customer = Customer::find($payment->customer_id);
            }



            if(!empty($attachment)){
                if($attachment->mime == 'application/pdf'){
                    $file = storage_path('app/assessment_attachments/'.$customer->company_number.'/'). $attachment->file_path;
                    if (file_exists($file)) {
                        $headers = ['Content-Type' => 'application/pdf'];
                        return response()->download($file, $attachment->file_name, $headers);
                    } else {
                        abort(404, 'File not found!');
                    }
                }elseif($attachment->mime == 'application/jpg' || $attachment->mime == 'application/png' || $attachment->mime == 'application/jpeg'){//if jpg/png/jpeg
                    $file = storage_path('app/assessment_attachments/'.$customer->company_number.'/'). $attachment->file_path;
                    if (file_exists($file)) {
                        $headers = ['Content-Type' => 'application/jpg'];
                        return response()->download($file, $attachment->file_name, $headers);
                    } else {
                        abort(404, 'File not found!');
                    }
                }elseif($attachment->mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'){//if word
                    $file = storage_path('app/assessment_attachments/'.$customer->company_number.'/'). $attachment->file_path;
                    if (file_exists($file)) {
                        $headers = ['Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                        return response()->download($file, $attachment->file_name, $headers);
                    } else {
                        abort(404, 'File not found!');
                    }
                }else{

                }
            }else{
                return \redirect()->back()->with('error-message','No attachment was found');
            }

        }else{
            dd('No record found.');
        }
    }

    public function assessmentItems($payment_id,$flag){
        $payment_id = decrypt($payment_id);
        if (!empty($payment_id)){
            $payments = PaymentFee::getAssessmentItems($payment_id);
            if (!empty($payments)){
                $attachments = AssessmentAttachment::getAssessmentAttachments($payment_id);
                return view('assessment.assessment.assessment_items')->with(compact('payments','attachments','flag'))
                    ->with('title','Assessment items');
            }else{
                return \redirect()->back()->with('title','Payments')->with('error-message','No record found.');
            }
        }else{
            return \redirect()->back()->with('title','Payments')->with('error-message','No reference found.');
        }
    }

    //continue assessment and allow assessment print
    public function continueAssessment(Request $request){
        $payment_id = decrypt($request->payment_id);//payment id from newly created payment for printing
        if (!empty($payment_id)){
            $payment = Payment::find($payment_id);
            if (empty($payment)){
                return \redirect()->to('new-assessment')->with('title','New assessment')->with('error-message','Failed to create and print assessment.');
            }

            $payment_id = $payment->id;
            $fee_accounts = array();
            foreach (FeeAccount::all() as $fee_account){
                $fee_accounts[$fee_account->id] = $fee_account->account_name;
            }

            $divisions = array();
            foreach (Division::all() as $division){
                $divisions[$division->id] = $division->division_name;
            }

            return view('assessment.assessment.new_assessment')->with('title'.'New assessment')
                ->with('title','Print assessment and add new')
                ->with('fee_accounts',$fee_accounts)
                ->with('divisions',$divisions)
                ->with('payment_id',$payment_id);
        }else{
            return \redirect()->to('new-assessment')->with('title','New assessment')->with('error-message','Failed to create and print assessment.');
        }
    }

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $invoice = substr(time(),-1).substr(strtoupper($randomString), -5);
        return  $invoice;
    }

    //save assessment
    public function saveAssessment(Request $request){

        DB::beginTransaction();
        try {
            $temp_payment_id = $request->temp_payment_id;
            $item_ids = $request->item_ids;
            $total_amount = $request->total_amount;


            $re_assessment = $request->re_assessment;

            if (empty($request->file('assessment_form_file'))){
                return \redirect()->back()->with('error-message','Please attach assessment form');
            }


            /*end exchange rate*/

            //get section details
            $temp_payment = TempPayment::find($temp_payment_id);
            if (!empty($temp_payment)){
                $accnt_code = $temp_payment->account_code;
                if ($accnt_code == 440321) {
                    $sc=1;
                }elseif($accnt_code == 440322){
                    $sc=2;
                }elseif($accnt_code == 440331){
                    $sc=3;
                }elseif($accnt_code == 440332){
                    $sc=4;
                }elseif($accnt_code == 440341){
                    $sc=5;
                }elseif($accnt_code == 440300){
                    $sc=6;
                }
                elseif($accnt_code == 440343){
                    $sc=8;
                }
                elseif($accnt_code == 440350){
                    $sc=6;
                }
                elseif($accnt_code == 440342){
                    $sc=7;
                }

            }

            //get the exchange rate
            $exr_max_id = ExchangeRate::max('id');
            if (!empty($exr_max_id)){
                $exchange_rate_info = ExchangeRate::find($exr_max_id);
            }


            if ($sc == 7){
                $exchange_rate = $exchange_rate_info->bl_exchange_rate;
            }else{
                $exchange_rate = $exchange_rate_info->exchange_rate;
            }

            if (!empty($temp_payment_id)){
                $temp_payment = TempPayment::getTempPaymentInfo($temp_payment_id);
                if (!empty($temp_payment)){
                    $check_payment = Payment::where('temp_payment_id','=',$temp_payment->id)->first();
                    //generate invoice

                    if (!empty($temp_payment->expire_days)){
                        $expire_days = $temp_payment->expire_days;
                    }else{
                        $expire_days = 7;
                    }

                    $invoice = substr($temp_payment->account_code, -2) . $this->generateRandomString();

                    $booking_date = date('Y-m-d H:i:s');
                    $expire_days = $expire_days;
                    $expire_date = date("Y-m-d H:i:s", strtotime($booking_date . '+ ' . $expire_days . ' days'));

                    if($re_assessment == 're_assessment'){
                        $status=1;
                        $re_assessment_description = $_POST['re_assessment_description'];
                    }else{
                        $re_assessment_description = '';
                        $status=0;
                    }

                    $curr = 'TZS';

                    if ($temp_payment->currency =='US $') {
                        $curr='USD';
                    }



                    if($re_assessment == 're_assessment'){
                        $flag = 'B_O';
                    }else{
                        $flag = 'BRELA';
                    }



                    if (empty($check_payment)){

                        //get customer info or add new
                        $company_number = $temp_payment->company_number;
                        $check_customer = Customer::checkCustomer($company_number);
                        if (empty($check_customer)){
                            $customer = new Customer();
                            $customer->company_number = $temp_payment->company_number;
                            $customer->customer_name = ucwords(strtolower($temp_payment->company_name));
                            $customer->user_id = Auth::user()->id;
                            $customer->save();
                            $customer_id = $customer->id;
                        }else{
                            $customer_id = $check_customer->id;
                        }

                        //create new entry in the payments table

                        $payment = new Payment();
                        $payment->user_id = Auth::user()->id;
                        $payment->customer_id =$customer_id;
                        $payment->temp_payment_id =$temp_payment->id;
                        $payment->amount = $total_amount;
                        $payment->cheque_amount = '';
                        $payment->payment_type = 'Cash';
                        $payment->cheque_amount = '';
                        $payment->date_of_payment = date('d/m/Y',strtotime(date('Y-m-d')));
                        $payment->month = date('m');
                        $payment->year = date('Y');
                        $payment->account_code = $temp_payment->account_code;
                        $payment->pay_type = 'none';
                        $payment->currency = $temp_payment->currency;
                        $payment->app_print = 'no';
                        $payment->regno = $temp_payment->company_number;
                        $payment->invoice = $invoice;
                        $payment->reference = $invoice;
                        $payment->re_assessment_description = $re_assessment_description;
                        $payment->add_date = date('Y-m-d');
                        $payment->save();

                        //return payment id
                        $payment_id = $payment->id;


                        /*Begin assessment attachment*/

                        $assessment_form_file=$request->file('assessment_form_file');

                        if (!empty($assessment_form_file)){

                            $extension = $assessment_form_file->getClientOriginalExtension();
                            if (strtolower($extension) == 'png' || strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg' || strtolower($extension) == 'pdf'){

                                // SET UPLOAD PATH

                                if (!File::isDirectory(storage_path().'/'.'app/assessment_attachments'.'/'.$company_number)){
                                    File::makeDirectory(storage_path().'/'.'app/assessment_attachments'.'/'.$company_number,0777,true);
                                }


                                $destinationPath = storage_path().'/'.'app/assessment_attachments'.'/'.$company_number;//getClientOriginalExtension
                                // GET THE FILE EXTENSION
                                $extension = $assessment_form_file->getClientOriginalExtension();

                                $fileName = date('YmdHis').'_'.$assessment_form_file->getClientOriginalName();
                                // MOVE THE UPLOADED FILES TO THE DESTINATION DIRECTORY
                                $upload_success = $assessment_form_file->move($destinationPath, $fileName);

                                //$path = 'report_attachments/'.$fileName;
                                $attachment = new AssessmentAttachment();
                                $attachment->payment_id = $payment_id;
                                //$attachment->file_path = 'assessment_attachments'.'/'.$company_number.'/'.date('YmdHis').'_'.$assessment_form_file->getClientOriginalName();
                                $attachment->file_path = date('YmdHis').'_'.$assessment_form_file->getClientOriginalName();
                                $attachment->mime = $assessment_form_file->getClientMimeType();
                                $attachment->file_name = $assessment_form_file->getClientOriginalName();
                                $attachment->extension = $extension;
                                $attachment->save();

                            }
                        }

                        /*End assessment attachment*/

                        //get items
                        $temp_items = TempItem::where('temp_payment_id','=',$temp_payment_id)->get();
                        if (!empty($temp_items)){
                            foreach ($temp_items as $temp_item){
                                //add entries in the payments table
                                $payment_fee = new PaymentFee();
                                $payment_fee->user_id = Auth::user()->id;
                                $payment_fee->payment_id = $payment_id;
                                $payment_fee->fee_item_id = $temp_item->fee_item_id;
                                $payment_fee->temp_payment_id = $temp_payment->id;
                                $payment_fee->fee_amount = $temp_item->fee_amount;
                                $payment_fee->date_of_payment = date('d/m/Y',strtotime(date('Y-m-d')));
                                $payment_fee->account_code = $temp_payment->account_code;
                                $payment_fee->month = date('m');
                                $payment_fee->year = date('Y');
                                $payment_fee->fname = $temp_item->fname;
                                $payment_fee->fyear2 = $temp_item->fyear2;
                                $payment_fee->fyear = $temp_item->fyear;
                                $payment_fee->save();

                                //create an array


                                //create an array for the summary

                                $fee_item = FeeItem::find($temp_item->fee_item_id);
                                $fee = Fee::find($fee_item->fee_id);

                                $feenamed=$fee->fee_name;
                                $fnamed=$temp_item->fname;
                                $fyeard=$temp_item->fyear;
                                $fnd=($temp_item->fname)?"$fnamed ($fyeard)":"";
                                $fee_amountd=$temp_item->fee_amount;
                                $gfs_code = $fee->gfs_code;


                                $str=$temp_payment->company_name;
                                $customer_name = str_replace("'", "", $str);

                                $smd[] = "{'description' : '$feenamed $fnd, 'name' : '$customer_name, 'amount' : '$fee_amountd, 'gfs_code' : '$gfs_code}";

                            }

                            $comma_separated = implode(", ", $smd);


                        }else{
                            return redirect()->back()->with('title','New assessment')->with('error-message','No items selected');
                        }

                        //update temp payment
                        $temp_pay = TempPayment::find($temp_payment->id);
                        $temp_pay->status = '1';
                        $temp_pay->save();


                        //create entries in obrs
                        DB::connection('pgsql')->table('booking')->insert(array('amount' => $total_amount,
                            'invoice' => $invoice,
                            'currency'=>$curr,
                            'section_id'=>$sc,
                            'summary'=>$comma_separated,
                            'booking_from'=>$flag,
                            're_assessment_from'=>$sc,
                            'reference'=>$invoice,
                            'phone_number'=>$temp_payment->phone_number,
                            'exchange_rate'=>$exchange_rate,
                            'bl_exchange_rate'=>$exchange_rate,
                            'expire_days'=>$expire_days,
                            'expire_date'=>$expire_date));


                        $booking_info = DB::connection('pgsql')->table('booking')->select()->where('reference',$invoice)->first();
                        $booking_id = $booking_info->booking_id;

                        $check_booking_invoice = DB::connection('pgsql')->table('brela_invoice')->select()->where('booking_id',$booking_id)->first();
                        if (empty($check_booking_invoice)){
                            DB::connection('pgsql')->table('brela_invoice')->insert(array('booking_id' => $booking_id,
                                'name' => $temp_payment->company_name));
                        }

                        //Put GePG processes here



                        /*End GePG content*/




                        DB::commit();
                        //sleep the process for 5 seconds to allow GePG processing
                        sleep(5);
                        return Redirect::route('continue-assessment', array('payment_id' => encrypt($payment_id)));
                        //return redirect()->back()->with('title','New assessment')->with('success-message','Assessment successfully')->with('payment_id',$payment_id);

                    }else{
                        return redirect()->back()->with('title','New assessment')->with('error-message','Already accessed');
                    }
                }else{
                    return redirect()->back()->with('title','New assessment')->with('error-message','No record found');
                }
            }else{
                return redirect()->back()->with('title','New assessment')->with('error-message','No reference found');
            }
        }catch (\Exception $exception){
            DB::rollBack();
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'AssessmentController','saveAssessment','assessment-error');
            return redirect()->back()->with('error-message',$message);
        }


    }

    public function pendingAssessmentDetails($tempAssessmentId){
        try {
            $tempAssessmentId = decrypt($tempAssessmentId);
            $temp_payment = TempPayment::find($tempAssessmentId);

            if (!empty($temp_payment)){
                $temp_items = TempItem::where('temp_payment_id','=',$temp_payment->id)->orderBy('fyear','DESC')->get();


                $fee_accounts = array();
                foreach (FeeAccount::all() as $fee_account){
                    $fee_accounts[$fee_account->id] = $fee_account->account_name;
                }

                $divisions = array();
                foreach (Division::all() as $division){
                    $divisions[$division->id] = $division->division_name;
                }

                $payment_id = 0;
                $feeAcc = FeeAccount::where(['account_code'=>$temp_payment->account_code])->first();
                $division = Division::find($feeAcc->division_id);

                $fees = array();
                foreach (Fee::where(['fee_account_id'=>$feeAcc->id])->get() as $fee){
                    $fees[$fee->id] = $fee->fee_name;
                }

                return view('assessment.assessment.pending_assessment_details')->with('title','New assessment')
                    ->with(compact('fee_accounts','divisions','payment_id','temp_items','temp_payment',
                        'division','feeAcc','fees'));
            }

        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'AssessmentController','pendingAssessmentDetails','assessment-error');
            return redirect()->back()->with('error-message',$message);
        }
    }

    public function deleteAssessment(Request $request){
        try {
            $id = $request->id;
            $assessment = TempPayment::where(['status'=>0])->first();
            if (!empty($assessment)){
                $checkTempItems = TempItem::where(['temp_payment_id'=>$assessment->id])->get();
                if (!empty($checkTempItems)){
                    foreach ($checkTempItems as $item){
                        $data = TempItem::find($item->id);
                        $data->delete();
                    }
                }

                $assessment->delete();
            }

            return response()->json(['success'=>1,'message'=>'Temporary assessment successfully deleted']);
        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'AssessmentController','deleteAssessment','assessment-error');
            return redirect()->back()->with('error-message',$message);
        }
    }

    public function pendingAssessment(){
        $tempAssessments = TempPayment::getPendingAssessments();
        return view('assessment.assessment.pending_assessment')->with('title','Pending assessment')->with(compact('tempAssessments'));
    }

    //get fee accounts
    public function removeItem(Request $request){
        $temp_item_id = $request->temp_item_id;
        $temp_payment_id = $request->temp_payment_id;
        if (!empty($temp_payment_id)){
            $temp_item = TempItem::find($temp_item_id);
            $temp_item->delete();
            $temp_payment = TempPayment::find($temp_payment_id);

            $temp_items = TempItem::getTempItems($temp_payment->id);
            return response(['company_number'=>$temp_payment->company_number,'company_name'=>$temp_payment->company_name,
                'filing_date'=>$temp_payment->filing_date, 'temp_payment_id'=>$temp_payment->id,'phone_number'=>$temp_payment->phone_number,
                'expire_days'=>$temp_payment->expire_days,
                'success'=>'1']);
        }else{
            return response(['success'=>2]);
        }

    }

    //get fee items
    public function getItems(Request $request){
        $fee_id = $request->fee_id;
        if (!empty($fee_id)){
            if (in_array($fee_id,array(7,8,15))){
                $items = FeeItem::getFeeItems($fee_id,'schedule A');
            }else{
                $items = FeeItem::getFeeItems($fee_id,null);
            }

            $fee_items = array();
            foreach ($items as $item){
                $fee_items[$item->id] = $item->item_name;
            }

            return view('assessment.fees.get_items')->with('title','Fee items')->with('fee_items',$fee_items);
        }else{
            return response()->json(['success'=>2]);
        }
    }

    public function checkFee(Request $request){
        $item_id = $request->item_id;
        $division_id = $request->division_id;
        $fee_account_id = $request->fee_account_id;
        $filing_date = $request->filing_date;
        $year = $request->year;
        $fee_id = $request->fee_id;
        $number_of_files = $request->number_of_files;

        if (!empty($number_of_files || $number_of_files != null)){
            $number_of_files = $number_of_files;
        }else{
            $number_of_files = '1';
        }

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

                if ($account_code == 440331){//company section

                    //check if it is filing
                    if (in_array($fee->id,array(18,19,20,21,69,70))){//it is filing

                        /*if ($filing_date == \date('Y-m-d')){
                            echo json_encode(array('success'=>'10'));
                            exit();
                        }*/

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

                                            //echo $calculation_date;
                                            //echo "<br>";


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
                                                    //$days_in_months = (int)round(($difference_days/30));
                                                    //$months = $days_in_months;

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
                                            //echo 'Initial amount: '.$initial_amount = $fee_amount + $item_amount;
                                            //echo "<br>";


                                        }else{


                                        }


                                    }

                                }else{//filing month is greater than current month,don't give any grace period
                                    //dd('The filing month is greater than current moth');



                                    //grant grace period to the year less to the current year
                                    for ($yr_diff; ($current_year - $yr_diff)<=$current_year; $yr_diff--){
                                        if ($current_year == ($current_year - $yr_diff)) {

                                        }else{

                                            //echo $current_year-$yr_diff;
                                            //echo "<br>";

                                            //grant grace period to the year before the current year
                                            if (($current_year - 1) == ($current_year - $yr_diff)){


                                                //check if to add grace period
                                                $calculation_year = ($current_year - $yr_diff);

                                                //allow grace period
                                                //$file_date = date("Y-m-d", strtotime($filing_date . '+ ' . $days . ' days'));
                                                //$file_date = date("Y-m-d", strtotime($filing_date . '+ ' . $days . ' days'));
                                                $file_month = date('m',strtotime($filing_date));


                                                $file_date = date("Y-m-d", strtotime($filing_date . '+ ' . $days . ' days'));
                                                //dd($file_date);
                                                $calculation_date = $calculation_year.'-'.$file_month.'-'.$filing_day;

                                                $calculation_date = date("Y-m-d", strtotime($calculation_date . '+ ' . $days . ' days'));

                                                //echo $calculation_date;
                                                //echo "<br>";


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
                                                        //$days_in_months = (int)round(($difference_days/30));
                                                        //$months = $days_in_months;

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
                                                //echo 'Initial: '.$initial_amount = $fee_amount + $item_amount;
                                                //echo "<br>";

                                            }else{
                                                //dd('Not the year less 1 value to the current yeardddd');
                                            }


                                        }


                                    }



                                }

                                //dd('End of first exec');


                                $total_amt = 0;

                                for ($ydiff; ($current_year - $ydiff)<=$current_year; $ydiff--){
                                    if ($current_year == ($current_year - $ydiff)){

                                        //dd('In if');



                                    }
                                    else{

                                        //decide which year to go back
                                        if ($filing_month <= $current_month){
                                            $calculation_year = ($current_year - $ydiff);

                                            //put those years which are not the same as the current year
                                            //echo $calculation_year;
                                            //echo "<br>";

                                            //$calculation_year = ($current_year - $ydiff);
                                            $calculation_date = $calculation_year.'-'.$filing_month.'-'.$filing_day;
                                            $calculation_date = date('Y-m-d',strtotime($calculation_date));
                                            //echo $calculation_date;
                                            //echo "<br>";


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

                                            //echo $months;
                                            //echo "<br>";exchange_rate_info

                                            $fee_amount = $months * $penalty;
                                            //echo "Penalty:" .$fee_amount;
                                            //echo "<br>";
                                            //echo $amt = $fee_amount + $initial_amount;
                                            $amt = $fee_amount + $initial_amount;
                                            //echo 'Amount plus penalty'.$amt;//store individual value and year
                                            //echo "<br>";
                                            //echo $total_amt = $total_amt + $amt;
                                            $total_amt = $total_amt + $amt;
                                            //echo "<br>";



                                        }else{//filing month greater than current month of the current year
                                            $calculation_year = ($current_year - ($ydiff + 1));

                                            if (($current_year - 1) == ($current_year - ($ydiff + 1))){
                                                //this year has been taken as a default year
                                            }else{

                                                //put those years which are not the same as the current year
                                                //echo $calculation_year;
                                                //echo "<br>";


                                                //$calculation_year = ($current_year - $ydiff);
                                                $calculation_date = $calculation_year.'-'.$filing_month.'-'.$filing_day;
                                                $calculation_date = date('Y-m-d',strtotime($calculation_date));
                                                //echo $calculation_date;
                                                //echo "<br>";


                                                $current_filing_date = $current_year.'-'.$filing_month.'-'.$filing_day;
                                                $current_filing_date = new \DateTime($current_filing_date);
                                                ;

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

                                                //echo $months;
                                                //echo "<br>";exchange_rate_info

                                                $fee_amount = $months * $penalty;
                                                //echo "Penalty:" .$fee_amount;
                                                //echo "<br>";
                                                //echo $amt = $fee_amount + $initial_amount;
                                                $amt = $fee_amount + $initial_amount;
                                                //echo 'Amount plus penalty'.$amt;//store individual value and year
                                                //echo "<br>";
                                                //echo $total_amt = $total_amt + $amt;
                                                $total_amt = $total_amt + $amt;
                                                //echo "<br>";
                                            }


                                        }


                                    }


                                    //dd('end file');
                                    $total_amount = $total_amt + $initial_amount;
                                    $total_amount;





                                }

                                //dd('End loop');

                                echo json_encode(array('has_form'=>$has_form,
                                    'item_name'=>$item_name,
                                    'item_amount'=>$total_amount,
                                    'penalty_amount'=>$penalty,
                                    'currency'=>$currency,
                                    'days'=>$days,
                                    'copy_charge'=>$copy_charges,
                                    'success'=>'1',
                                    'number_of_files'=>$number_of_files));


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
                                        //$days_in_months = (int)round(($difference_days/30));
                                        //$months = $days_in_months;

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



                                //create response
                                echo json_encode(array('has_form'=>$has_form,
                                    'item_name'=>$item_name,
                                    'item_amount'=>$total_amount,
                                    'penalty_amount'=>$penalty,
                                    'currency'=>$currency,
                                    'days'=>$days,
                                    'copy_charge'=>$copy_charges,
                                    'success'=>'1',
                                    'number_of_files'=>$number_of_files));

                            }

                        }
                        else{// filing but no forms
                            $total_amount = $item_amount;
                            $penalty = $penalty;
                            $currency = $currency;
                            $days = $days;
                            $copy_charges = $copy_charges;

                            echo json_encode(array('has_form'=>$has_form,
                                'item_name'=>$item_name,
                                'item_amount'=>$total_amount,
                                'penalty_amount'=>$penalty,
                                'currency'=>$currency,
                                'days'=>$days,
                                'copy_charge'=>$copy_charges,
                                'success'=>'1',
                                'number_of_files'=>$number_of_files));


                        }
                    }
                    else{//not filing

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
                    elseif (in_array($fee->id, array(9,13,41,49,60))){//change fees
                        $total_amount = $fee_item->item_amount;
                        $penalty = $penalty;
                        $currency = $currency;
                        $days = $days;
                        $copy_charges = $copy_charges;
                    }
                    elseif ($fee->id == 66){
                        $total_amount = $number_of_files * $item_amount;
                        $penalty = $penalty;
                        $currency = $currency;
                        $days = $days;
                        $copy_charges = $copy_charges;
                    }else{
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
                    echo json_encode(array('has_form'=>$has_form,
                        'item_name'=>$item_name,
                        'item_amount'=>$total_amount,
                        'penalty_amount'=>$penalty,
                        'currency'=>$currency,
                        'days'=>$days,
                        'copy_charge'=>$copy_charges,
                        'success'=>'1',
                        'number_of_files'=>$number_of_files));

                }
                elseif ($account_code == 440342){

                    if (strtolower($item_name) == 'business licence penalties'){
                        $current_date = new \DateTime(date('Y-m-d'));
                        $filing_date = new \DateTime(\date('Y-m-d',strtotime($filing_date . '+ ' . $days . ' days')));
                        $diff = $current_date->diff($filing_date);
                        $number_of_years = $diff->y;
                        $months = $diff->m;

                        $penalty_value = 0;
                        $total_amount = 0;
                        if ($months >= 1){
                            for ($i = 0; $i < $months; $i++){
                                $penalty_value = $penalty + ($i * 0.02);
                                //$total_amount = $total_amount + ($item_amount * $penalty_value);
                                $total_amount = $item_amount * $penalty_value;
                                //echo $total_amount;
                                //echo "<br>";
                            }
                        }else{
                            $total_amount = $item_amount;
                            $penalty = $penalty;
                            $currency = $currency;
                            $days = $days;
                            $copy_charges = $copy_charges;
                        }
                    }elseif (strtolower($item_name) == 'business licence fees'){
                        $total_amount = $item_amount;
                        $penalty = $penalty;
                        $currency = $currency;
                        $days = $days;
                        $copy_charges = $copy_charges;
                    }else{
                        dd('ggg');

                    }


                    //return response as json
                    echo json_encode(array('has_form'=>$has_form,
                        'item_name'=>$item_name,
                        'item_amount'=>$total_amount,
                        'penalty_amount'=>$penalty,
                        'currency'=>$currency,
                        'days'=>$days,
                        'copy_charge'=>$copy_charges,
                        'success'=>'1',
                        'number_of_files'=>$number_of_files));

                }
                else{//Not any code
                    return response()->json(['success'=>12]);//Invalid account code
                }
            }


        }else{
            return response()->json(['success'=>2]);
        }
    }

    //get fee accounts
    public function displayFields(Request $request){
        $item_id = $request->item_id;
        if (!empty($item_id)){
            $fee_item = FeeItem::find($item_id);
            $fee = Fee::find($fee_item->fee_id);
            $has_form = $fee->has_form;

            if ($has_form == 'yes'){
                $item_name = $fee_item->item_name;
            }else{
                $fee = Fee::find($fee_item->fee_id);
                $item_name = $fee->fee_name;
            }

            return response()->json(['has_form'=>$has_form,'item_name'=>$item_name,'item_amount'=>$fee_item->item_amount,
                'penalty_amount'=>$fee_item->penalty_amount,'currency'=>$fee_item->currency,
                'days'=>$fee_item->days,'cp_charge'=>$fee_item->copy_charge,'success'=>1]);

        }else{
            return response()->json(['success'=>2]);
        }
    }
    public function addAssessmentFee(Request $request){

        //receive all inputs
        $company_number = $request->company_number;
        $company_name = $request->company_name;
        $item_id = $request->item_id;
        $division_id = $request->division_id;
        $fee_account_id = $request->fee_account_id;
        $filing_date = $request->filing_date;
        $fDate = $filing_date;
        $fyear = (int)$request->year;
        $fee_id = $request->fee_id;
        $item_amount = $request->item_amount;
        $item_name = $request->item_name;

        $phone_number = $request->phone_number;
        $expire_days = $request->expire_days;

        $number_of_files = $request->number_of_files;

        //check if year is selected

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

                $fee_amount = $item_amount;
                $current_date = date('Y-m-d');
                $current_year = date('Y');
                $date_of_payment = date('d/m/Y',strtotime($current_date));
                $month = date('m');
                $has_form = $fee->has_form;

                if ($has_form == 'yes'){
                    $item_name = $fee_item->item_name;
                }else{
                    $fee = Fee::find($fee_item->fee_id);
                    $item_name = $fee->fee_name;
                }


                if ($has_form == 'yes'){

                    //check if the item contains the name form or something else
                    $flag1 = 'registration';
                    $flag2 = 'filing';
                    $flag3 = 'stamp';
                    $flag4 = 'filling';
                    $flag5 = 'perusal';

                    if (in_array($fee->id,array(18,19,20,21,25,26,69,70))){
                        $type = $fee->fee_name;
                        $form = $fee_item->item_name;
                        $item_name = $fee_item->item_name;
                    }elseif (in_array($fee->id, array(23,27,41,42,43,44,45,46))){
                        $type = $fee->fee_name;
                        $form = $fee_item->item_name;
                        $item_name = $fee_item->item_name;
                    }else{
                        $fee = Fee::find($fee_item->fee_id);
                        $item_name = $fee->fee_name;
                        $type = $fee_item->fee_name;
                        $form = '';
                    }


                    /*$check_column = FeeItem::where('item_name','LIKE','%'.$flag1.'%')
                        ->orWhere('item_name','LIKE','%'.$flag2.'%')
                        ->orWhere('item_name','LIKE','%'.$flag3.'%')
                        ->orWhere('item_name','LIKE','%'.$flag4.'%')
                        ->orWhere('item_name','LIKE','%'.$flag5.'%')
                        ->where('id','=',$item_id)->first();

                    if (empty($check_column)){
                        $type = $fee->fee_name;
                        $form = $fee_item->item_name;
                        $item_name = $fee_item->item_name;
                    }else{
                        $fee = Fee::find($fee_item->fee_id);
                        $item_name = $fee->fee_name;
                        $type = $fee_item->fee_name;
                        $form = '';
                    }*/

                }else{
                    $fee = Fee::find($fee_item->fee_id);
                    $item_name = $fee->fee_name;
                    $type = $fee_item->fee_name;
                    $form = '';
                }


                $fee_account = FeeAccount::find($fee_account_id);
                if (!empty($fee_account)){
                    $account_code = $fee_account->account_code;
                }else{
                    $account_code = 0;
                }

                //check_company in temp payment
                $check_if_exists = TempPayment::where('company_number','=',$company_number)->where('status','=','0')->first();
                if (empty($check_if_exists)){

                    $temp_payment = new TempPayment();
                    $temp_payment->user_id = Auth::user()->id;
                    $temp_payment->company_number = $company_number;
                    $temp_payment->account_code = $account_code;
                    $temp_payment->company_name = ucwords(strtolower($company_name));
                    $temp_payment->filing_date = $filing_date;
                    $temp_payment->currency = $currency;
                    $temp_payment->phone_number = $phone_number;
                    $temp_payment->expire_days = $expire_days;
                    $temp_payment->save();

                    $temp_payment_id = $temp_payment->id;

                }else{
                    $temp_payment_id = $check_if_exists->id;
                }



                if ($account_code == 440331){//company section

                    //check if it is filing
                    if (in_array($fee->id, array(18,19,20,21,69,70))){//it is filing

                        if ($has_form == 'yes'){//it has forms


                            $current_month = (int)date('m');


                            $current_date = date('Y-m-d');
                            $curr_date = new \DateTime($current_date);
                            $current_year = (int)date('Y');
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
                            $filing_month = (int)date('m',strtotime($filing_date));
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

                                            //echo $calculation_date;
                                            //echo "<br>";


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
                                                    //$days_in_months = (int)round(($difference_days/30));
                                                    //$months = $days_in_months;

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


                                            }
                                            else{//calculation greater than today date
                                                $months = 0;
                                            }



                                            $fee_amount = $months * $penalty;
                                            $initial_amount = $fee_amount + $item_amount;
                                            //echo 'Initial amount: '.$initial_amount = $fee_amount + $item_amount;
                                            //echo "<br>";
                                            $checkTempItem = TempItem::where(['fee_item_id'=>$item_id,'temp_payment_id'=>$temp_payment_id,'fyear'=>$current_year,'fyear2'=>$current_year])->first();

                                            //save initial amount if year not selected by user
                                            if ($fyear == 0){
                                                //start save initial amount as a first entry of payment
                                                if (empty($checkTempItem)){
                                                    $temp_item = new TempItem();
                                                    $temp_item->user_id = Auth::user()->id;
                                                    $temp_item->fee_item_id = $item_id;
                                                    $temp_item->temp_payment_id = $temp_payment_id;
                                                    //$temp_item->fee_amount = $item_amount;
                                                    $temp_item->fee_amount = $initial_amount;
                                                    $temp_item->date_of_payment = $date_of_payment;
                                                    $temp_item->account_code = $account_code;
                                                    $temp_item->month = $month;
                                                    $temp_item->year = $current_year;
                                                    $temp_item->fname = $form;
                                                    $temp_item->fyear2 = $current_year;
                                                    $temp_item->fyear = $current_year;
                                                    $temp_item->filing_date = date('d/m/Y',strtotime($fDate));
                                                    $temp_item->save();
                                                }

                                                //End save initial amount
                                            }


                                            //start save initial amount as a first entry of payment
                                            if ($fyear == $current_year){
                                                if (empty($checkTempItem)){
                                                    $temp_item = new TempItem();
                                                    $temp_item->user_id = Auth::user()->id;
                                                    $temp_item->fee_item_id = $item_id;
                                                    $temp_item->temp_payment_id = $temp_payment_id;
                                                    //$temp_item->fee_amount = $item_amount;
                                                    $temp_item->fee_amount = $initial_amount;
                                                    $temp_item->date_of_payment = $date_of_payment;
                                                    $temp_item->account_code = $account_code;
                                                    $temp_item->month = $month;
                                                    $temp_item->year = $current_year;
                                                    $temp_item->fname = $form;
                                                    $temp_item->fyear2 = $current_year;
                                                    $temp_item->fyear = $current_year;
                                                    $calculation_date = $current_year.'-'.$filing_month.'-'.\date('d',strtotime($filing_date));
                                                    $temp_item->filing_date = date('d/m/Y',strtotime($calculation_date));
                                                    $temp_item->save();
                                                }
                                            }

                                            /*End save initial amount*/



                                        }
                                        else{
                                            /**
                                             * Do nothing for previous years
                                             */
                                        }
                                    }

                                }
                                else{//filing month is greater than current month,don't give any grace period
                                    //dd('The filing month is greater than current moth');



                                    //grant grace period to the year less to the current year
                                    for ($yr_diff; ($current_year - $yr_diff)<=$current_year; $yr_diff--){
                                        if ($current_year == ($current_year - $yr_diff)) {
                                            /**
                                             *exclude current year
                                             */
                                        }else{

                                            //echo $current_year-$yr_diff;
                                            //echo "<br>";
                                            //grant grace period to the year before the current year
                                            if (($current_year - 1) == ($current_year - $yr_diff)){


                                                //check if to add grace period
                                                $calculation_year = ($current_year - $yr_diff);

                                                //allow grace period
                                                //$file_date = date("Y-m-d", strtotime($filing_date . '+ ' . $days . ' days'));
                                                //$file_date = date("Y-m-d", strtotime($filing_date . '+ ' . $days . ' days'));
                                                $file_month = date('m',strtotime($filing_date));


                                                $file_date = date("Y-m-d", strtotime($filing_date . '+ ' . $days . ' days'));
                                                $calculation_date = $calculation_year.'-'.$file_month.'-'.$filing_day;
                                                $calculation_date = date("Y-m-d", strtotime($calculation_date . '+ ' . $days . ' days'));


                                                //echo $calculation_date;
                                                //echo "<br>";


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
                                                        //$days_in_months = (int)round(($difference_days/30));
                                                        //$months = $days_in_months;

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


                                                }
                                                else{//calculation greater than today date
                                                    $months = 0;
                                                }

                                                $fee_amount = $months * $penalty;
                                                $initial_amount = $fee_amount + $item_amount;
                                                //echo 'Initial: '.$initial_amount = $fee_amount + $item_amount;
                                                //echo "<br>";

                                                $checkTempItem = TempItem::where(['fee_item_id'=>$item_id,'temp_payment_id'=>$temp_payment_id,'fyear'=>$calculation_year,'fyear2'=>$calculation_year])->first();
                                                //start save other amount for payment
                                                if ($fyear == 0){
                                                    if (empty($checkTempItem)){
                                                        $temp_item = new TempItem();
                                                        $temp_item->user_id = Auth::user()->id;
                                                        $temp_item->fee_item_id = $item_id;
                                                        $temp_item->temp_payment_id = $temp_payment_id;
                                                        $temp_item->fee_amount = $initial_amount;
                                                        $temp_item->date_of_payment = $date_of_payment;
                                                        $temp_item->account_code = $account_code;
                                                        $temp_item->month = $month;
                                                        $temp_item->year = $current_year;
                                                        $temp_item->fname = $form;
                                                        $temp_item->fyear2 = $calculation_year;
                                                        $temp_item->fyear = $calculation_year;
                                                        $temp_item->filing_date = date('d/m/Y',strtotime($fDate));
                                                        $temp_item->save();
                                                    }
                                                }else{
                                                    if ($fyear == $calculation_year){
                                                        if (empty($checkTempItem)){
                                                            $temp_item = new TempItem();
                                                            $temp_item->user_id = Auth::user()->id;
                                                            $temp_item->fee_item_id = $item_id;
                                                            $temp_item->temp_payment_id = $temp_payment_id;
                                                            $temp_item->fee_amount = $initial_amount;
                                                            $temp_item->date_of_payment = $date_of_payment;
                                                            $temp_item->account_code = $account_code;
                                                            $temp_item->month = $month;
                                                            $temp_item->year = $current_year;
                                                            $temp_item->fname = $form;
                                                            $temp_item->fyear2 = $calculation_year;
                                                            $temp_item->fyear = $calculation_year;
                                                            $calculation_date = $calculation_year.'-'.$filing_month.'-'.\date('d',strtotime($filing_date));
                                                            //$temp_item->filing_date = date('d/m/Y',strtotime($calculation_date));
                                                            $temp_item->filing_date = date('d/m/Y',strtotime($fDate));
                                                            $temp_item->save();
                                                        }
                                                    }
                                                }

                                                //End save payment amount

                                            }
                                            else{
                                                /**
                                                 * Not the year less 1 value to the current year
                                                 */
                                            }


                                        }


                                    }



                                }


                                //dd($initial_amount);
                                //dd('End of first exec');


                                $total_amt = 0;

                                for ($ydiff; ($current_year - $ydiff)<=$current_year; $ydiff--){
                                    if ($current_year == ($current_year - $ydiff)){
                                        /**
                                         * Do nothing for the current year
                                         */
                                    }
                                    else{

                                        //decide which year to go back
                                        if ($filing_month <= $current_month){
                                            $calculation_year = ($current_year - $ydiff);

                                            //put those years which are not the same as the current year
                                            //echo $calculation_year;
                                            //echo "<br>";

                                            //$calculation_year = ($current_year - $ydiff);
                                            $calculation_date = $calculation_year.'-'.$filing_month.'-'.$filing_day;
                                            $calculation_date = date('Y-m-d',strtotime($calculation_date));
                                            //echo $calculation_date;
                                            //echo "<br>";


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

                                            //echo $months;
                                            //echo "<br>";exchange_rate_info

                                            $fee_amount = $months * $penalty;
                                            //echo "Penalty:" .$fee_amount;
                                            //echo "<br>";
                                            //echo $amt = $fee_amount + $initial_amount;
                                            $amt = $fee_amount + $initial_amount;
                                            //echo 'Amount plus penalty'.$amt;//store individual value and year
                                            //echo "<br>";
                                            //echo $total_amt = $total_amt + $amt;
                                            $total_amt = $total_amt + $amt;
                                            //echo "<br>";
                                            $checkTempItem = TempItem::where(['fee_item_id'=>$item_id,'temp_payment_id'=>$temp_payment_id,'fyear'=>$calculation_year,'fyear2'=>$calculation_year])->first();

                                            //start save other amount for payment
                                            if (empty($checkTempItem)){

                                                if ($fyear == 0){
                                                    $temp_item = new TempItem();
                                                    $temp_item->user_id = Auth::user()->id;
                                                    $temp_item->fee_item_id = $item_id;
                                                    $temp_item->temp_payment_id = $temp_payment_id;
                                                    $temp_item->fee_amount = $amt;
                                                    $temp_item->date_of_payment = $date_of_payment;
                                                    $temp_item->account_code = $account_code;
                                                    $temp_item->month = $month;
                                                    $temp_item->year = $current_year;
                                                    $temp_item->fname = $form;
                                                    $temp_item->fyear2 = $calculation_year;
                                                    $temp_item->fyear = $calculation_year;
                                                    $calculation_date = $calculation_year.'-'.$filing_month.'-'.\date('d',strtotime($filing_date));
                                                    //$temp_item->filing_date = date('d/m/Y',strtotime($calculation_date));
                                                    $temp_item->filing_date = date('d/m/Y', strtotime($fDate));
                                                    $temp_item->save();
                                                }else{
                                                    if ($fyear == $calculation_year){
                                                        $temp_item = new TempItem();
                                                        $temp_item->user_id = Auth::user()->id;
                                                        $temp_item->fee_item_id = $item_id;
                                                        $temp_item->temp_payment_id = $temp_payment_id;
                                                        $temp_item->fee_amount = $amt;
                                                        $temp_item->date_of_payment = $date_of_payment;
                                                        $temp_item->account_code = $account_code;
                                                        $temp_item->month = $month;
                                                        $temp_item->year = $current_year;
                                                        $temp_item->fname = $form;
                                                        $temp_item->fyear2 = $calculation_year;
                                                        $temp_item->fyear = $calculation_year;
                                                        $temp_item->filing_date = date('d/m/Y',strtotime($fDate));
                                                        $temp_item->save();
                                                    }
                                                }

                                            }
                                            //End save payment amount


                                        }
                                        else{//filing month greater than current month of the current year
                                            $calculation_year = ($current_year - ($ydiff + 1));

                                            if (($current_year - 1) == ($current_year - ($ydiff + 1))){
                                                //this year has been taken as a default year
                                            }else{

                                                //put those years which are not the same as the current year
                                                //echo $calculation_year;
                                                //echo "<br>";


                                                //$calculation_year = ($current_year - $ydiff);
                                                $calculation_date = $calculation_year.'-'.$filing_month.'-'.$filing_day;
                                                $calculation_date = date('Y-m-d',strtotime($calculation_date));
                                                //echo $calculation_date;
                                                //echo "<br>";


                                                $current_filing_date = $current_year.'-'.$filing_month.'-'.$filing_day;
                                                $current_filing_date = new \DateTime($current_filing_date);
                                                ;

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

                                                //echo $months;
                                                //echo "<br>";exchange_rate_info

                                                $fee_amount = $months * $penalty;
                                                //echo "Penalty:" .$fee_amount;
                                                //echo "<br>";
                                                //echo $amt = $fee_amount + $initial_amount;
                                                $amt = $fee_amount + $initial_amount;
                                                //echo 'Amount plus penalty'.$amt;//store individual value and year
                                                //echo "<br>";
                                                //echo $total_amt = $total_amt + $amt;
                                                $total_amt = $total_amt + $amt;
                                                //echo "<br>";



                                                /*if ($fyear == $calculation_year){
                                                    //start save other amount for payment
                                                    $temp_item = new TempItem();
                                                    $temp_item->user_id = Auth::user()->id;
                                                    $temp_item->fee_item_id = $item_id;
                                                    $temp_item->temp_payment_id = $temp_payment_id;
                                                    $temp_item->fee_amount = $amt;
                                                    $temp_item->date_of_payment = $date_of_payment;
                                                    $temp_item->account_code = $account_code;
                                                    $temp_item->month = $month;
                                                    $temp_item->year = $current_year;
                                                    $temp_item->fname = $form;
                                                    $temp_item->fyear2 = $calculation_year;
                                                    $temp_item->fyear = $calculation_year;
                                                    $temp_item->filing_date = date('d/m/Y',strtotime($filing_date));
                                                    $temp_item->save();

                                                    //End save payment amount
                                                }*/

                                                //start save other amount for payment
                                                /*if ($fyear == $calculation_year){

                                                }*/

                                                $temp_item = new TempItem();
                                                $temp_item->user_id = Auth::user()->id;
                                                $temp_item->fee_item_id = $item_id;
                                                $temp_item->temp_payment_id = $temp_payment_id;
                                                $temp_item->fee_amount = $amt;
                                                $temp_item->date_of_payment = $date_of_payment;
                                                $temp_item->account_code = $account_code;
                                                $temp_item->month = $month;
                                                $temp_item->year = $current_year;
                                                $temp_item->fname = $form;
                                                $temp_item->fyear2 = $calculation_year;
                                                $temp_item->fyear = $calculation_year;
                                                $calculation_date = $calculation_year.'-'.$filing_month.'-'.\date('d',strtotime($filing_date));
                                                $temp_item->filing_date = date('d/m/Y',strtotime($calculation_date));
                                                $temp_item->save();

                                                //End save payment amount

                                            }


                                        }


                                    }


                                    //dd('end file');
                                    $total_amount = $total_amt + $initial_amount;
                                    $total_amount;





                                }

                                //dd('End loop');

                                return response()->json(['has_form'=>$has_form, 'item_name'=>$item_name, 'item_amount'=>$total_amount, 'penalty_amount'=>$penalty,
                                    'currency'=>$currency, 'days'=>$days, 'copy_charge'=>$copy_charges, 'success'=>'1', 'temp_payment_id'=>$temp_payment_id,
                                    'company_number'=>$company_number, 'company_name'=>$company_name, 'filling_date'=>$filing_date, 'phone_number'=>$phone_number,
                                    'expire_days'=>$expire_days, 'number_of_files'=>$number_of_files]);

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
                                        //$days_in_months = (int)round(($difference_days/30));
                                        //$months = $days_in_months;

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

                                /*if ($fyear == $calculation_year){
                                    //Save item payment
                                    $temp_item = new TempItem();
                                    $temp_item->user_id = Auth::user()->id;
                                    $temp_item->fee_item_id = $item_id;
                                    $temp_item->temp_payment_id = $temp_payment_id;
                                    $temp_item->fee_amount = $total_amount;
                                    $temp_item->date_of_payment = $date_of_payment;
                                    $temp_item->account_code = $account_code;
                                    $temp_item->month = $month;
                                    $temp_item->year = $current_year;
                                    $temp_item->fname = $form;
                                    $temp_item->fyear2 = $calculation_year;
                                    $temp_item->fyear = $calculation_year;
                                    $temp_item->filing_date = date('d/m/Y',strtotime($filing_date));
                                    $temp_item->save();

                                    //End save item amount
                                }*/

                                //Save item payment
                                /*if ($fyear == $calculation_year){

                                }*/

                                $temp_item = new TempItem();
                                $temp_item->user_id = Auth::user()->id;
                                $temp_item->fee_item_id = $item_id;
                                $temp_item->temp_payment_id = $temp_payment_id;
                                $temp_item->fee_amount = $total_amount;
                                $temp_item->date_of_payment = $date_of_payment;
                                $temp_item->account_code = $account_code;
                                $temp_item->month = $month;
                                $temp_item->year = $current_year;
                                $temp_item->fname = $form;
                                $temp_item->fyear2 = $calculation_year;
                                $temp_item->fyear = $calculation_year;
                                $calculation_date = $calculation_year.'-'.$filing_month.'-'.\date('d',strtotime($filing_date));
                                $temp_item->filing_date = date('d/m/Y',strtotime($calculation_date));
                                $temp_item->save();

                                /*End save item amount*/

                                //create response
                               return response()->json(['has_form'=>$has_form, 'item_name'=>$item_name, 'item_amount'=>$total_amount, 'penalty_amount'=>$penalty,
                                   'currency'=>$currency, 'days'=>$days, 'copy_charge'=>$copy_charges, 'success'=>'1', 'temp_payment_id'=>$temp_payment_id,
                                   'company_number'=>$company_number, 'company_name'=>$company_name, 'filling_date'=>$filing_date, 'phone_number'=>$phone_number,
                                   'expire_days'=>$expire_days, 'number_of_files'=>$number_of_files]);

                            }

                        }
                        else{// filing but no forms
                            $total_amount = $item_amount;
                            $penalty = $penalty;
                            $currency = $currency;
                            $days = $days;
                            $copy_charges = $copy_charges;

                            return response()->json(['has_form'=>$has_form, 'item_name'=>$item_name, 'item_amount'=>$total_amount, 'penalty_amount'=>$penalty,
                                'currency'=>$currency, 'days'=>$days, 'copy_charge'=>$copy_charges, 'success'=>'1', 'temp_payment_id'=>$temp_payment_id,
                                'company_number'=>$company_number, 'company_name'=>$company_name, 'filling_date'=>$filing_date, 'phone_number'=>$phone_number,
                                'expire_days'=>$expire_days, 'number_of_files'=>$number_of_files]);


                        }
                    }
                    else{//not filing

                        if ($fee->id == 61){//Amendment
                            $total_amount = $item_amount;
                            $penalty = $penalty;
                            $currency = $currency;
                            $days = $days;
                            $copy_charges = $copy_charges;
                        }
                        elseif ($fee->id == 52){//Stamp duty
                            $total_amount = $item_amount;
                            $penalty = $penalty;
                            $currency = $currency;
                            $days = $days;
                            $copy_charges = $copy_charges;
                        }
                        elseif (in_array($fee->id, array(25,26,27))){//Late filing

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

                                            //echo $calculation_date;
                                            //echo "<br>";


                                            $today_date = date('Y-m-d');

                                            if ($calculation_date < $today_date){

                                                //$file_date = new \DateTime($file_date);

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
                                                    //$days_in_months = (int)round(($difference_days/30));
                                                    //$months = $days_in_months;

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
                                            //$initial_amount = $fee_amount + $item_amount;
                                            //echo 'Initial amount: '.$initial_amount = $fee_amount + $item_amount;
                                            //echo "<br>";


                                        }
                                        else{
                                            /**
                                             * Do nothing the previous years
                                             */
                                        }


                                    }

                                }
                                else{//filing month is greater than current month,don't give any grace period
                                    //dd('The filing month is greater than current moth');



                                    //grant grace period to the year less to the current year
                                    for ($yr_diff; ($current_year - $yr_diff)<=$current_year; $yr_diff--){
                                        if ($current_year == ($current_year - $yr_diff)) {
                                            /**
                                             * Do nothing the current year
                                             */
                                        }else{

                                            //echo $current_year-$yr_diff;
                                            //echo "<br>";

                                            //grant grace period to the year before the current year
                                            if (($current_year - 1) == ($current_year - $yr_diff)){


                                                //check if to add grace period
                                                $calculation_year = ($current_year - $yr_diff);

                                                //allow grace period
                                                //$file_date = date("Y-m-d", strtotime($filing_date . '+ ' . $days . ' days'));
                                                //$file_date = date("Y-m-d", strtotime($filing_date . '+ ' . $days . ' days'));
                                                $file_month = date('m',strtotime($filing_date));


                                                $file_date = date("Y-m-d", strtotime($filing_date . '+ ' . $days . ' days'));
                                                //dd($file_date);
                                                $calculation_date = $calculation_year.'-'.$file_month.'-'.$filing_day;

                                                $calculation_date = date("Y-m-d", strtotime($calculation_date . '+ ' . $days . ' days'));

                                                //echo $calculation_date;
                                                //echo "<br>";


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
                                                        //$days_in_months = (int)round(($difference_days/30));
                                                        //$months = $days_in_months;

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
                                                //$initial_amount = $fee_amount + $item_amount;
                                                //echo 'Initial: '.$initial_amount = $fee_amount + $item_amount;
                                                //echo "<br>";

                                            }else{
                                                //dd('Not the year less 1 value to the current yeardddd');
                                            }


                                        }


                                    }



                                }


                                //dd($initial_amount);
                                // dd('End loop');
                                $total_amount = $fee_amount;


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
                                }
                                else{


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
                                        //$days_in_months = (int)round(($difference_days/30));
                                        //$months = $days_in_months;

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


                            }


                            //End late filing




                        }
                        elseif ($fee->id == 31){//Perusal
                            $total_amount = $number_of_files * $item_amount;
                            $penalty = $penalty;
                            $currency = $currency;
                            $days = $days;
                            $copy_charges = $copy_charges;
                        }
                        elseif (in_array($fee->id, array(10,11,14,30,42,43,51))){
                            /**
                             * Certifying Fees (companies certificates)Certifying Fees (companies memorandum)Change Fees (companies Particulars)Name Reservation Fees
                            Registration Fees (companies - local)Registration Fees (companies -Foreign)Search Fees- Official (companies)
                             */
                            $total_amount = $item_amount;
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
                            $copy_charges = 0;;
                        }

                        $checkItem = TempItem::where(['fee_item_id'=>$item_id,'temp_payment_id'=>$temp_payment_id])->first();
                        if (empty($checkItem)){
                            $temp_item = new TempItem();
                            $temp_item->user_id = Auth::user()->id;
                            $temp_item->fee_item_id = $item_id;
                            $temp_item->temp_payment_id = $temp_payment_id;
                            $temp_item->fee_amount = $total_amount;
                            $temp_item->date_of_payment = $date_of_payment;
                            $temp_item->account_code = $account_code;
                            $temp_item->month = $month;
                            $temp_item->year = $current_year;
                            $temp_item->fname = $form;
                            $temp_item->fyear2 = $fyear;
                            $temp_item->fyear = $fyear;
                            $temp_item->save();
                        }else{
                            return response()->json(['success'=>4]);
                        }

                        return response()->json(['has_form'=>$has_form, 'item_name'=>$item_name, 'item_amount'=>$total_amount, 'penalty_amount'=>$penalty,
                            'currency'=>$currency, 'days'=>$days, 'copy_charge'=>$copy_charges, 'success'=>'1', 'temp_payment_id'=>$temp_payment_id,
                            'company_number'=>$company_number, 'company_name'=>$company_name, 'filling_date'=>$filing_date, 'phone_number'=>$phone_number, 'expire_days'=>$expire_days,
                            'number_of_files'=>$number_of_files]);

                    }


                }
                elseif ($account_code == 440332){//Business Names

                    if ($fee->id = 2){//payment of annual maintenance fee

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
                    }elseif ($fee->id == 13){//change fees
                        $total_amount = $fee_item->item_amount;
                        $penalty = $penalty;
                        $currency = $currency;
                        $days = $days;
                        $copy_charges = $copy_charges;
                    }elseif ($fee->id == 66){
                        $total_amount = $number_of_files * $item_amount;
                        $penalty = $penalty;
                        $currency = $currency;
                        $days = $days;
                        $copy_charges = $copy_charges;
                    }elseif ($fee->id == 41){
                        $total_amount = $item_amount;
                        $penalty = $penalty;
                        $currency = $currency;
                        $days = $days;
                        $copy_charges = $copy_charges;
                    }

                    elseif ($account_code == 440341){
                        $total_amount = $item_amount;
                        $penalty = $penalty;
                        $currency = $currency;
                        $days = $days;
                        $copy_charges = $copy_charges;


                        //return response as json
                        echo json_encode(array('has_form'=>$has_form,
                            'item_name'=>$item_name,
                            'item_amount'=>$total_amount,
                            'penalty_amount'=>$penalty,
                            'currency'=>$currency,
                            'days'=>$days,
                            'copy_charge'=>$copy_charges,
                            'success'=>'1',
                            'number_of_files'=>$number_of_files));

                    }elseif ($account_code == 440342){

                        if ($fee->id == 8){
                            $current_date = new \DateTime(date('Y-m-d'));
                            $filing_date = new \DateTime(\date('Y-m-d',strtotime($filing_date . '+ ' . $days . ' days')));
                            $diff = $current_date->diff($filing_date);
                            $number_of_years = $diff->y;
                            $months = $diff->m;

                            $penalty_value = 0;
                            $total_amount = 0;
                            if ($months >= 1){
                                for ($i = 0; $i < $months; $i++){
                                    $penalty_value = $penalty + ($i * 0.02);
                                    //$total_amount = $total_amount + ($item_amount * $penalty_value);
                                    $total_amount = $item_amount * $penalty_value;
                                    //echo $total_amount;
                                    //echo "<br>";
                                }
                            }else{
                                $total_amount = $item_amount;
                                $penalty = $penalty;
                                $currency = $currency;
                                $days = $days;
                                $copy_charges = $copy_charges;
                            }
                        }
                        elseif ($fee->id == 7){
                            $total_amount = $item_amount;
                            $penalty = $penalty;
                            $currency = $currency;
                            $days = $days;
                            $copy_charges = $copy_charges;
                        }else{

                        }

                    }

                    //save entry
                    $temp_item = new TempItem();
                    $temp_item->user_id = Auth::user()->id;
                    $temp_item->fee_item_id = $item_id;
                    $temp_item->temp_payment_id = $temp_payment_id;
                    $temp_item->fee_amount = $total_amount;
                    $temp_item->date_of_payment = $date_of_payment;
                    $temp_item->account_code = $account_code;
                    $temp_item->month = $month;
                    $temp_item->year = $current_year;
                    $temp_item->fname = $form;
                    $temp_item->fyear2 = $fyear;
                    $temp_item->fyear = $fyear;
                    $temp_item->save();

                    //return response as json
                    echo json_encode(array('has_form'=>$has_form,
                        'item_name'=>$item_name,
                        'item_amount'=>$total_amount,
                        'penalty_amount'=>$penalty,
                        'currency'=>$currency,
                        'days'=>$days,
                        'copy_charge'=>$copy_charges,
                        'success'=>'1',
                        'temp_payment_id'=>$temp_payment_id,
                        'company_number'=>$company_number,
                        'company_name'=>$company_name,
                        'filling_date'=>$filing_date,
                        'phone_number'=>$phone_number,
                        'expire_days'=>$expire_days,
                        'number_of_files'=>$number_of_files));


                }else{//something else

                }


            }else{
                //no fee record found
            }


        }else{
            //rhe item ID is not available,no reference found
            echo json_encode(array('success'=>'2'));
        }





    }

    public function getSelectedItems(Request $request){
        $company_number = $request->company_number;
        if (!empty($company_number)){

            $temp_payment = TempPayment::where('company_number',$company_number)->where('status','=','0')->first();
            if (!empty($temp_payment)){
                $temp_items = TempItem::where('temp_payment_id','=',$temp_payment->id)->orderBy('fyear','DESC')->get();
                return view('assessment.assessment.get_selected_items')->with('temp_items',$temp_items)->with('temp_payment',$temp_payment);
            }else{

            }


        }else{
            echo json_encode(array('success'=>'2'));
        }
    }

    public function newAssessment(){

        $fee_accounts = array();
        foreach (FeeAccount::all() as $fee_account){
            $fee_accounts[$fee_account->id] = $fee_account->account_name;
        }

        $divisions = array();
        foreach (Division::all() as $division){
            $divisions[$division->id] = $division->division_name;
        }

        $payment_id = 0;

        return view('assessment.assessment.new_assessment')->with('title','New assessment')
            ->with('fee_accounts',$fee_accounts)->with('divisions',$divisions)->with('payment_id',$payment_id);
    }

    public function printAssessment(Request $request){
        $payment_id = decrypt($request->payment_id);

        if (!empty($payment_id)){
            $paymentInfo = Payment::find($payment_id);
            $customer = Customer::find($paymentInfo->customer_id);
            $payerName = $customer->customer_name;
            $applicantName = $payerName;

            $paymentItems = PaymentFee::getAssessmentItems($payment_id);

            $booking = Booking::getBookingInfo($paymentInfo->invoice);
            $expireDate = date('Y-m-d', strtotime($booking->expire_date));
            $customerName = $payerName;
            $amount = $booking->amount;
            $currency = $booking->currency;

            $user = User::find($paymentInfo->user_id);

            $data = [
                "opType"=>"20000",
                "shortCode"=>"0010010000",
                "billReference"=>"$booking->reference",
                "amount"=>"$amount",
                "billCcy"=>"$currency",
                "billExprDt"=>"$expireDate",
                "billPayOpt"=>"3",
                "billRsv01"=>"Business Registrations and Licensing Agency|$customerName"
            ];

            $amountInWords = trim(CurrencyNumberToWordConverter::convertNumber($amount,$currency));


            $qrcodedata = json_encode($data, true);

            return view('assessment.invoice.invoice')
                ->with(compact('paymentInfo','paymentItems','payerName','qrcodedata',
                    'amountInWords','applicantName','booking','user'))
                ->with('title','Print assessment');

        }else{
            return \redirect()->to('new-assessment')->with('title','New assessment');
        }

    }

    public function generatedAssessments($flag){

        $payments = Payment::getAssessmentRecords($flag);
        $flag = ucfirst($flag);

        return view('assessment.assessment.generated_assessments')->with('title',$flag)->with(compact('payments','flag'));
    }

}
