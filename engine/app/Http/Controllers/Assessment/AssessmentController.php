<?php

namespace App\Http\Controllers\Assessment;

use App\Helpers\CurrencyNumberToWordConverter;
use App\Http\Controllers\Billing\BillingController;
use App\Http\Controllers\Controller;
use App\Models\Assessment\AssessmentAttachment;
use App\Models\Assessment\Division;
use App\Models\Assessment\EventLog;
use App\Models\Assessment\Fee;
use App\Models\Assessment\FeeAccount;
use App\Models\Assessment\FeeItem;
use App\Models\Billing\Billing;
use App\Models\Booking\Booking;
use App\Models\Customer\Customer;
use App\Models\ExchangeRate\ExchangeRate;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentFee;
use App\Models\Payment\TempItem;
use App\Models\Payment\TempPayment;
use App\Models\SystemConfig\SystemConfig;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class AssessmentController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function billCalculationCriteria(){
        return view('assessment.assessment.bl_calculation_criteria');
    }

    public function searchAssessment(Request $request){

        try {

            $entityName = $request->entityName;
            $entityNumber = $request->entityNumber;
            $flag = $request->flag;
            $controlNumber = $request->controlNumber;
            $reference = $request->reference;
            $searchType = $request->searchType;

            if ($searchType == 'receipt'){

                if (strtolower($flag) == 'individual'){

                    if (!empty($entityName)){
                        $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                            ->join('users as u','u.id','=','p.accountantId')
                            ->select('p.id as id','company_number','customer_name','currency','invoice','billAmount','paidAmount','customer_id','date_of_payment','p.accountantId','name','isPaid','p.user_id')
                            ->where('customer_name','LIKE','%'.$entityName.'%')->where(['p.accountantId'=>Auth::user()->id])->where(['isPaid'=>1])->get();
                    }elseif (!empty($entityNumber)){
                        $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                            ->join('users as u','u.id','=','p.accountantId')
                            ->select('p.id as id','company_number','customer_name','currency','invoice','billAmount','paidAmount','customer_id','date_of_payment','p.accountantId','name','isPaid','p.user_id')
                            ->where('company_number','LIKE','%'.$entityNumber.'%')->where(['p.accountantId'=>Auth::user()->id])->where(['isPaid'=>1])->get();
                    }elseif (!empty($controlNumber)){
                        //dd(Auth::user()->id);
                        $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                            ->join('users as u','u.id','=','p.accountantId')
                            ->select('p.id as id','company_number','customer_name','currency','invoice','billAmount','paidAmount','customer_id','date_of_payment','p.accountantId','name','isPaid','p.user_id')
                            ->where('controlNumber','LIKE','%'.$controlNumber.'%')->where(['p.accountantId'=>Auth::user()->id])->where(['isPaid'=>1])->get();
                    }elseif (!empty($reference)){
                        $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                            ->join('users as u','u.id','=','p.accountantId')
                            ->select('p.id as id','company_number','customer_name','currency','invoice','billAmount','paidAmount','customer_id','date_of_payment','p.accountantId','name','isPaid','p.user_id')
                            ->where('reference','LIKE','%'.$reference.'%')->where(['p.accountantId'=>Auth::user()->id])->where(['isPaid'=>1])->get();
                    }else{
                        $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                            ->join('users as u','u.id','=','p.accountantId')
                            ->select('p.id as id','company_number','customer_name','currency','invoice','billAmount','paidAmount','customer_id','date_of_payment','p.accountantId','name','isPaid','p.user_id')
                            ->where(['p.accountantId'=>Auth::user()->id])->where(['isPaid'=>1])->get();
                    }
                }
                else{

                    if (!empty($entityName)){
                        $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                            ->join('users as u','u.id','=','p.accountantId')
                            ->select('p.id as id','company_number','customer_name','currency','invoice','billAmount','paidAmount','customer_id','date_of_payment','p.accountantId','name','isPaid','p.user_id')
                            ->where('customer_name','LIKE','%'.$entityName.'%')->where(['isPaid'=>1])->get();
                    }elseif (!empty($entityNumber)){
                        $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                            ->join('users as u','u.id','=','p.accountantId')
                            ->select('p.id as id','company_number','customer_name','currency','invoice','billAmount','paidAmount','customer_id','date_of_payment','p.accountantId','name','isPaid','p.user_id')
                            ->where('company_number','LIKE','%'.$entityNumber.'%')->where(['isPaid'=>1])->get();
                    }elseif (!empty($controlNumber)){
                        $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                            ->join('users as u','u.id','=','p.accountantId')
                            ->select('p.id as id','company_number','customer_name','currency','invoice','billAmount','paidAmount','customer_id','date_of_payment','p.accountantId','name','isPaid','p.user_id')
                            ->where('controlNumber','LIKE','%'.$controlNumber.'%')->where(['isPaid'=>1])->get();
                    }elseif (!empty($reference)){
                        $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                            ->join('users as u','u.id','=','p.accountantId')
                            ->select('p.id as id','company_number','customer_name','currency','invoice','billAmount','paidAmount','customer_id','date_of_payment','p.accountantId','name','isPaid','p.user_id')
                            ->where('reference','LIKE','%'.$reference.'%')->where(['isPaid'=>1])->get();
                    }else{
                        $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                            ->join('users as u','u.id','=','p.accountantId')
                            ->select('p.id as id','company_number','customer_name','currency','invoice','billAmount','paidAmount','customer_id','date_of_payment','p.accountantId','name','isPaid','p.user_id')
                            ->where(['isPaid'=>1])->get();
                    }
                }

                $view = "receipt_list";
            }else{
                if (strtolower($flag) == 'individual'){

                    if (!empty($entityName)){
                        $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                            ->join('users as u','u.id','=','p.user_id')
                            ->select('p.id as id','company_number','customer_name','currency','invoice','billAmount','paidAmount','customer_id','date_of_payment','p.user_id','name','isPaid')
                            ->where('customer_name','LIKE','%'.$entityName.'%')->where(['p.user_id'=>Auth::user()->id])->get();
                    }elseif (!empty($entityNumber)){
                        $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                            ->join('users as u','u.id','=','p.user_id')
                            ->select('p.id as id','company_number','customer_name','currency','invoice','billAmount','paidAmount','customer_id','date_of_payment','p.user_id','name','isPaid')
                            ->where('company_number','LIKE','%'.$entityNumber.'%')->where(['p.user_id'=>Auth::user()->id])->get();
                    }elseif (!empty($controlNumber)){
                        $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                            ->join('users as u','u.id','=','p.user_id')
                            ->select('p.id as id','company_number','customer_name','currency','invoice','billAmount','paidAmount','customer_id','date_of_payment','p.user_id','name','isPaid')
                            ->where('controlNumber','LIKE','%'.$controlNumber.'%')->where(['p.user_id'=>Auth::user()->id])->get();
                    }elseif (!empty($reference)){
                        $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                            ->join('users as u','u.id','=','p.user_id')
                            ->select('p.id as id','company_number','customer_name','currency','invoice','billAmount','paidAmount','customer_id','date_of_payment','p.user_id','name','isPaid')
                            ->where('reference','LIKE','%'.$reference.'%')->where(['p.user_id'=>Auth::user()->id])->get();
                    }else{
                        $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                            ->join('users as u','u.id','=','p.user_id')
                            ->select('p.id as id','company_number','customer_name','currency','invoice','billAmount','paidAmount','customer_id','date_of_payment','p.user_id','name','isPaid')
                            ->where(['p.user_id'=>Auth::user()->id])->get();
                    }
                }
                else{

                    if (!empty($entityName)){
                        $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                            ->join('users as u','u.id','=','p.user_id')
                            ->select('p.id as id','company_number','customer_name','currency','invoice','billAmount','paidAmount','customer_id','date_of_payment','p.user_id','name','isPaid')
                            ->where('customer_name','LIKE','%'.$entityName.'%')->get();
                    }elseif (!empty($entityNumber)){
                        $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                            ->join('users as u','u.id','=','p.user_id')
                            ->select('p.id as id','company_number','customer_name','currency','invoice','billAmount','paidAmount','customer_id','date_of_payment','p.user_id','name','isPaid')
                            ->where('company_number','LIKE','%'.$entityNumber.'%')->get();
                    }elseif (!empty($controlNumber)){
                        $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                            ->join('users as u','u.id','=','p.user_id')
                            ->select('p.id as id','company_number','customer_name','currency','invoice','billAmount','paidAmount','customer_id','date_of_payment','p.user_id','name','isPaid')
                            ->where('controlNumber','LIKE','%'.$controlNumber.'%')->get();
                    }elseif (!empty($reference)){
                        $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                            ->join('users as u','u.id','=','p.user_id')
                            ->select('p.id as id','company_number','customer_name','currency','invoice','billAmount','paidAmount','customer_id','date_of_payment','p.user_id','name','isPaid')
                            ->where('reference','LIKE','%'.$reference.'%')->get();
                    }else{
                        $payments = DB::table('payments as p')->join('customers as c','c.id','=','p.customer_id')
                            ->join('users as u','u.id','=','p.user_id')
                            ->select('p.id as id','company_number','customer_name','currency','invoice','billAmount','paidAmount','customer_id','date_of_payment','p.user_id','name','isPaid')
                            ->get();
                    }
                }
                $view = "assessment_list";
            }

            $flag = ucfirst($flag);

            return view('assessment.assessment.'.$view)
                ->with('title',$flag)->with('payments',$payments)->with('flag',$flag);

        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'AssessmentController','searchAssessment','assessment-error');
            return redirect()->back()->with('error-message',$message);
        }
    }


    public function getFileContent($attachmentId){

        if(!empty($attachmentId)){

            $attachment = AssessmentAttachment::where('id','=',$attachmentId)->first();

            if (!empty($attachment)){
                $tempPay = TempPayment::find($attachment->temp_payment_id);
                $cust = Customer::where(['company_number'=>$tempPay->company_number])->first();
                $payment = Payment::find($attachment->payment_id);
                if (!empty($payment)){
                    $customer = Customer::find($payment->customer_id);
                }else{
                    $customer = $cust;
                }
            }

            $companyNumber = $customer->company_number ?? $tempPay->company_number;



            if(!empty($attachment)){
                if($attachment->mime == 'application/pdf'){
                    $file = storage_path('app/assessment_attachments/'.$companyNumber.'/'). $attachment->file_path;
                    if (file_exists($file)) {
                        $headers = ['Content-Type' => 'application/pdf'];
                        return response()->download($file, $attachment->file_name, $headers);
                    } else {
                        abort(404, 'File not found!');
                    }
                }elseif($attachment->mime == 'application/jpg' || $attachment->mime == 'application/png' || $attachment->mime == 'application/jpeg'){//if jpg/png/jpeg
                    $file = storage_path('app/assessment_attachments/'.$companyNumber.'/'). $attachment->file_path;
                    if (file_exists($file)) {
                        $headers = ['Content-Type' => 'application/jpg'];
                        return response()->download($file, $attachment->file_name, $headers);
                    } else {
                        abort(404, 'File not found!');
                    }
                }elseif($attachment->mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'){//if word
                    $file = storage_path('app/assessment_attachments/'.$companyNumber.'/'). $attachment->file_path;
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
        $tempStatus = $request->tempStatus;

        $fee_accounts = array();
        foreach (FeeAccount::all() as $fee_account){
            $fee_accounts[$fee_account->id] = $fee_account->account_name;
        }

        $divisions = array();
        foreach (Division::all() as $division){
            $divisions[$division->id] = $division->division_name;
        }


        if (in_array($tempStatus, array(2,3))){
            $payment = null;
            return view('assessment.assessment.new_assessment')->with('title','New assessment')
                ->with('title','Print assessment and add new')->with(compact('fee_accounts','divisions','payment_id','payment','tempStatus'));
        }

        if (!empty($payment_id)){
            $payment = Payment::find($payment_id);
            if (empty($payment)){
                return \redirect()->to('new-assessment')->with('title','New assessment')->with('error-message','Failed to create and print assessment.');
            }

            $payment_id = $payment->id;


            return view('assessment.assessment.new_assessment')->with('title'.'New assessment')
                ->with('title','Print assessment and add new')->with(compact('fee_accounts','divisions','payment_id','payment','tempStatus'));
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

    function generateRandomNumbers($length = 10){
        $characters = '0123456789098765432101234567890';
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

        //DB::beginTransaction();
        try {
            $temp_payment_id = $request->temp_payment_id;
            $item_ids = $request->item_ids;
            $total_amount = $request->total_amount;
            $tempStatus = $request->tempStatus;
            $company_number = $request->company_number;

            $checkTempPay = TempPayment::getPendingTempAssessmentByCompanyNumber($company_number);


            $re_assessment = $request->re_assessment;
            $checkAttachment = AssessmentAttachment::checkAttachment($temp_payment_id);

            if ($tempStatus == 0 || empty($checkAttachment)){
                if (empty($request->file('assessment_form_file'))){
                    return response()->json(['success'=>4,'message'=>'Please attach assessment form']);
                    //return \redirect()->back()->with('error-message','Please attach assessment form');
                }
            }


            if (empty($checkTempPay)){
                return response()->json(['success'=>5,'message'=>'Please make assessment before requesting control number']);
            }

            $checkItems = TempItem::getTempItems($checkTempPay->id);
            if (empty($checkItems)){
                return response()->json(['success'=>5,'message'=>'Please add fee items before requesting control number']);
            }




            /*Begin assessment attachment*/

            $assessment_form_file=$request->file('assessment_form_file');

            if ($tempStatus == 0 || empty($checkAttachment)){
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


                        $filePath = date('YmdHis').'_'.$assessment_form_file->getClientOriginalName();
                        $mimeType = $assessment_form_file->getClientMimeType();
                        $fileName = $assessment_form_file->getClientOriginalName();
                        $attachment = AssessmentAttachment::saveAttachment($temp_payment_id,$filePath,$mimeType,$fileName,$extension);


                    }
                }
            }
            else{

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


                        $filePath = date('YmdHis').'_'.$assessment_form_file->getClientOriginalName();
                        $mimeType = $assessment_form_file->getClientMimeType();
                        $fileName = $assessment_form_file->getClientOriginalName();
                        AssessmentAttachment::updateAttachmentFile($temp_payment_id,$filePath,$mimeType,$fileName,$extension);


                    }
                }

            }

            /*End assessment attachment*/


            if (in_array($tempStatus, array(2,3))){

                $tempPayment = TempPayment::find($temp_payment_id);

                if (!empty($tempPayment)){
                    TempPayment::updateTempStatus($temp_payment_id,$tempStatus);
                }
                //DB::commit();
                if ($tempStatus == 2){
                    $message = 'Assessment has been successfully forwarded to supervisor before invoice generation';
                }elseif ($tempStatus == 3){
                    $message = 'Assessment has been successfully forwarded to accountant for invoice generation';
                }

                return response()->json(['success'=>1,'message'=>$message,'tempStatus'=>$tempStatus,'payment_id'=>encrypt($temp_payment_id)]);
                //return \redirect()->to('assessments/new-assessment')->with('title','New assessment')->with('success-message',$message);

            }


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
                }elseif($accnt_code == 440342){
                    $sc=6;
                }
                elseif($accnt_code == 440350 || $accnt_code == 440300){
                    $sc=7;
                }
                elseif($accnt_code == 440343){
                    $sc=8;
                }


            }

            //get the exchange rate
            $exchange_rate_info = ExchangeRate::getExchangeRate();

            if (!empty($exchange_rate_info)){
                $exchange_rate = $exchange_rate_info->exchange_rate;
            }else{
                $exchange_rate = 2250;
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

                    $invoice = substr($temp_payment->account_code, -2) . $this->generateRandomNumbers();

                    $booking_date = date('Y-m-d H:i:s');
                    $expire_days = $expire_days;
                    $expire_date = date("Y-m-d H:i:s", strtotime($booking_date . '+ ' . $expire_days . ' days'));

                    $curr = $temp_payment->currency ?? 'TZS';
                    $flag = 'BRELA';



                    if (empty($check_payment)){

                        //get customer info or add new
                        $company_number = $temp_payment->company_number;
                        $entityType = $temp_payment->entityType;
                        $regDate = $temp_payment->regDate;
                        $check_customer = Customer::checkCustomer($company_number,$entityType);
                        if (empty($check_customer)){
                            $customer = Customer::saveCustomer($temp_payment->company_number,$temp_payment->company_name,$entityType,$regDate);
                            $customer_id = $customer->id;
                        }else{
                            if (empty($check_customer->regDate)){
                                $check_customer->regDate = $regDate;
                                $check_customer->save();
                                $check_customer = Customer::checkCustomer($company_number,$entityType);
                                $customer_id = $check_customer->id;
                            }

                            $customer_id = $check_customer->id;
                        }

                        $re_assessment_description = null;

                        //get last booking id
                        $bookingId = Booking::getLastBooking();

                        //create new booking record
                        $bookingId = $bookingId + 1;

                        //create new entry in the payments table
                        $payment = Payment::savePayment($customer_id,$temp_payment->id,$total_amount,$temp_payment->account_code,
                            $temp_payment->currency,$temp_payment->company_number,$invoice,$re_assessment_description,$bookingId,
                            $temp_payment->calculationType,$temp_payment->licenceType,$temp_payment->phone_number,$entityType,$regDate);

                        //return payment id
                        $payment_id = $payment->id;


                        /**
                         * update attachment table with payment_id
                         */
                        $attach = AssessmentAttachment::updateAssessmentAttachment($temp_payment_id,$payment_id);

                        //get items
                        $temp_items = TempItem::where(['temp_payment_id'=>$temp_payment_id,'isToPermanent'=>0])->get();
                        $smd = array();
                        if (!empty($temp_items)){
                            foreach ($temp_items as $temp_item){
                                //add entries in the payments table
                                $payment_fee = PaymentFee::savePaymentItems($payment_id,$temp_item->fee_item_id,$temp_payment->id,$temp_item->fee_amount,
                                    $temp_payment->account_code,$temp_item->fname,$temp_item->fyear2,$temp_item->fyear);

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
                        $temp_pay->status = $tempStatus;
                        $temp_pay->save();


                        //update payment entries
                        $paymentInfo = Payment::updatePayment($payment_id,$total_amount,$invoice,$curr,$sc,$comma_separated,$flag,$temp_payment->phone_number,$exchange_rate,$expire_days,$expire_date,'normal','National Microfinance Bank');


                        $bookingInfo = Booking::createBooking($bookingId, $payment->reference);

                        $bookingId = $bookingInfo->bookingId;

                        //save local bill
                        $bill = Billing::saveBill($total_amount,$curr,$company_number,$bookingId,$payment->reference);

                        //Start GePG processing here

                        $booking = Payment::getPaymentInfoByBookingId($bookingId);
                        if ((int)$booking->invoice < initialControlNumber()){
                            if (!empty($bill)){

                                $data = BillingController::generateBill($payment->reference,$payment_id);
                                $response = $data->getData()->result;
                                $message = $data->getData()->message;

                            }else{
                                $response = 2;
                                $message = "Failed to get control number";
                            }
                        }else{
                            $response = 1;
                            $message = "The Bill control number has already been received";
                        }



                        /*End GePG content*/

                        //sleep the process for 5 seconds to allow GePG processing
                        sleep(9);

                        if ((int)$booking->invoice < initialControlNumber()){
                            //call function to save and update control number
                            $payment = Payment::getPaymentInfoByBookingId($bookingId);
                            $data = BillingController::receiveAndUpdateBillControlNumber($response,$payment->reference,$payment->invoice,$bill->billId,$message);
                            //DB::commit();

                            $result = $data->getData()->result;
                            $message = $data->getData()->message;
                            $status = $data->getData()->status;
                        }else{
                            $result = 1;
                        }

                        if (/*$tempStatus == 2*/ $result == 1){
                            $message = Auth::user()->name.' '.$message." for entity number: ".$company_number;
                        }elseif ($result == 3){
                            $message == $message;
                        }else{
                            $message = Auth::user()->name.' '.$message." for entity number: ".$company_number;
                        }



                        Log::channel('assessment')->info($message);
                        EventLog::saveEvent(Auth::user()->email,'System access','User', Auth::user()->name,$status,'Generate invoice',
                            $message,EventLog::getIpAddress(),EventLog::getMacAddress(),'AssessmentController','saveAssessment');
                        sleep(1);

                        return response()->json(['success'=>$result,'message'=>$message,'payment_id'=>encrypt($payment_id)]);
                        return Redirect::route('continue-assessment', array('payment_id' => encrypt($payment_id)));
                        //return redirect()->back()->with('title','New assessment')->with('success-message','Assessment successfully')->with('payment_id',$payment_id);

                    }else{
                        $message = 'Already accessed';
                        return response()->json(['success'=>0,'message'=>$message,'payment_id'=>$check_payment->id]);
                        return redirect()->back()->with('title','New assessment')->with('error-message',$message);
                    }
                }else{
                    $message = 'No payment record was found in temporary storage';
                    return response()->json(['success'=>0,'message'=>$message]);
                    return redirect()->back()->with('title','New assessment')->with('error-message',$message);
                }
            }else{
                $message = 'No reference found';
                return response()->json(['success'=>0,'message'=>$message]);
                return redirect()->back()->with('title','New assessment')->with('error-message',$message);
            }
        }catch (\Exception $exception){
            //DB::rollBack();
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'AssessmentController','saveAssessment','assessment-error');
            return response()->json(['success'=>0,'message'=>$message]);
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

                $attachments = AssessmentAttachment::getAssessmentTempAttachments($tempAssessmentId);

                return view('assessment.assessment.pending_assessment_details')->with('title','New assessment')
                    ->with(compact('fee_accounts','divisions','payment_id','temp_items','temp_payment',
                        'division','feeAcc','fees','attachments'));
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
            $assessment = TempPayment::whereIn('status', array(0,2))->where(['user_id'=>Auth::user()->id])->first();
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

            if (!empty($temp_item)){
                $temp_item->delete();
                $temp_payment = TempPayment::find($temp_payment_id);
                $temp_items = TempItem::getTempItems($temp_payment->id);
                return response(['company_number'=>$temp_payment->company_number,'company_name'=>$temp_payment->company_name,
                    'filing_date'=>$temp_payment->filing_date, 'temp_payment_id'=>$temp_payment->id,'phone_number'=>$temp_payment->phone_number,
                    'expire_days'=>$temp_payment->expire_days,
                    'success'=>'1']);
            }else{
                return response()->json(['success'=>2]);
            }
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


                                                if ($difference_days >= 30){
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

            $defineFeeAmount = $fee_item->defineFeeAmount;
            $accountCode = $fee->account_code;

            return response()->json(['has_form'=>$has_form,'item_name'=>$item_name,'item_amount'=>$fee_item->item_amount,
                'penalty_amount'=>$fee_item->penalty_amount,'currency'=>$fee_item->currency,
                'days'=>$fee_item->days,'cp_charge'=>$fee_item->copy_charge,'success'=>1,'defineFeeAmount'=>$defineFeeAmount,'accountCode'=>$accountCode]);

        }else{
            return response()->json(['success'=>2]);
        }
    }
    public function addAssessmentFee(Request $request){

        //receive all inputs
        $company_number = $request->company_number;
        $company_name = $request->company_name;
        $entityType = $request->entityType;
        $regDate = $request->regDate;
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
        $penaltyAmount = 0;

        $f = Fee::find($fee_id);
        if ($f->account_code == 440342){
            $accountCode = $f->account_code;
            $calculationType = $request->calculationType;
            $licenceType = $request->licenceType;
        }else{
            $calculationType = 1;
            $licenceType = 1;
            $accountCode = $f->account_code;
        }


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


                if ($fee_item->defineFeeAmount == 1){
                    $item_amount = $item_amount;
                }else{
                    $item_amount = $fee_item->item_amount;
                }

                //$item_amount = $fee_item->item_amount;
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
                $check_if_exists = TempPayment::where(['company_number'=>$company_number,'entityType'=>$entityType,'status'=>0])->first();
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
                    $temp_payment->calculationType = $calculationType;
                    $temp_payment->licenceType = $licenceType;
                    $temp_payment->entityType = $entityType;
                    $temp_payment->regDate = $regDate;
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
                                    'expire_days'=>$expire_days, 'number_of_files'=>$number_of_files,'calculationType'=>$calculationType,'licenceType'=>$licenceType,
                                    'entityType'=>$entityType,'regDate'=>$regDate,'accountCode'=>$accountCode]);

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
                                   'expire_days'=>$expire_days, 'number_of_files'=>$number_of_files,'calculationType'=>$calculationType,'licenceType'=>$licenceType,
                                   'entityType'=>$entityType,'regDate'=>$regDate,'accountCode'=>$accountCode]);

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
                                'expire_days'=>$expire_days, 'number_of_files'=>$number_of_files,'calculationType'=>$calculationType,'licenceType'=>$licenceType,
                                'entityType'=>$entityType,'regDate'=>$regDate,'accountCode'=>$accountCode]);


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
                            'number_of_files'=>$number_of_files,'calculationType'=>$calculationType,'licenceType'=>$licenceType,
                            'entityType'=>$entityType,'regDate'=>$regDate,'accountCode'=>$accountCode]);

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
                    }
                    elseif (in_array($fee->id, array(9,13,41,49,60))){
                        $total_amount = $item_amount;
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
                    }

                }
                elseif ($account_code == 440341){
                    $total_amount = $item_amount;
                    $penalty = $penalty;
                    $currency = $currency;
                    $days = $days;
                    $copy_charges = $copy_charges;
                }
                elseif ($account_code == 440342){


                    if ($fee->id == 7){//business licence

                        $ExpireDate = $filing_date;
                        $days = 21;

                        $applyFeeInUsd = $fee_item->applyFeeInUsd;
                        $principalUsdFee = $fee_item->principalUsdFee;
                        $principalTzsFee = $fee_item->principalTzsFee;
                        $branchUsdFee = $fee_item->branchUsdFee;
                        $branchTzsFee = $fee_item->branchTzsFee;
                        $scheduleType = $fee_item->licenceType;
                        $isPerUnitFeeApplicable = $fee_item->isPerUnitFeeApplicable;
                        $perUnitlicenseFeeTShs = $fee_item->perUnitlicenseFeeTShs;
                        $perUnitlicenseFeeUSD = $fee_item->perUnitlicenseFeeUSD;
                        $categoryId = $fee_item->categoryId;


                        if ($calculationType == 1){
                            $sharePercentLocal = 100;
                            $sharePercentForeign= 0;
                        }else{
                            $sharePercentLocal = 0;
                            $sharePercentForeign= 100;
                        }

                        $data = GeneralController::businessLicenceFeeCalculator($applyFeeInUsd,$categoryId,$sharePercentForeign,$sharePercentLocal,$isPerUnitFeeApplicable,$branchUsdFee,
                            $perUnitlicenseFeeUSD,$number_of_files,$branchTzsFee,$perUnitlicenseFeeTShs,$ExpireDate,$principalUsdFee,$principalTzsFee,$licenceType);

                        $billAmount = $data->getData()->billAmount;
                        $amountWithPenalty = $data->getData()->amountWithPenalty;
                        $penaltyAmount = $data->getData()->penaltyAmount;



                    }

                    $total_amount = $billAmount;
                    $penalty = $penaltyAmount;

                    if ($calculationType == 1){
                        $currency = 'TSHs';
                    }else{
                        $currency = 'US $';
                    }



                }
                elseif ($account_code == 440322){

                    if ($has_form == 'yes'){

                    }else{

                        $total_amount = $item_amount;
                        $penalty = $penalty;
                        $currency = $currency;
                        $days = $days;
                        $copy_charges = $copy_charges;

                    }

                }
                else{//something else
                    $total_amount = 0;
                    $penalty = 0;
                    $currency = 'TZS';
                    $days = 0;
                    $copy_charges = 0;
                }


                //save entry

                if ($account_code == 440342){

                    if ($penalty > 0){

                        $checkTempItem = TempItem::where(['fee_item_id'=>8,'temp_payment_id'=>$temp_payment_id])->first();//penalty fee
                        if (empty($checkTempItem)){

                            $temp_item = new TempItem();
                            $temp_item->user_id = Auth::user()->id;
                            $temp_item->fee_item_id = $item_id;
                            $temp_item->temp_payment_id = $temp_payment_id;
                            $temp_item->fee_amount = $penalty;
                            $temp_item->date_of_payment = $date_of_payment;
                            $temp_item->account_code = $account_code;
                            $temp_item->month = $month;
                            $temp_item->year = $current_year;
                            $temp_item->fname = $form;
                            $temp_item->fyear2 = $fyear;
                            $temp_item->fyear = $fyear;
                            $temp_item->save();


                        }

                        $checkTempItem = TempItem::where(['fee_item_id'=>$item_id,'temp_payment_id'=>$temp_payment_id])->first();//penalty fee
                        if (empty($checkTempItem)){

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

                        }



                    }else{

                        $checkTempItem = TempItem::where(['fee_item_id'=>$item_id,'temp_payment_id'=>$temp_payment_id])->first();//penalty fee
                        if (empty($checkTempItem)){

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


                        }

                    }

                }else{
                    $checkTempItem = TempItem::where(['fee_item_id'=>$item_id,'temp_payment_id'=>$temp_payment_id])->first();
                    if (empty($checkTempItem)){

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
                }



                //return response as json
                return response()->json(['has_form'=>$has_form, 'item_name'=>$item_name, 'item_amount'=>$total_amount, 'penalty_amount'=>$penalty, 'currency'=>$currency,
                    'days'=>$days, 'copy_charge'=>$copy_charges, 'success'=>'1', 'temp_payment_id'=>$temp_payment_id, 'company_number'=>$company_number,
                    'company_name'=>$company_name, 'filling_date'=>$filing_date, 'phone_number'=>$phone_number, 'expire_days'=>$expire_days,
                    'number_of_files'=>$number_of_files,'calculationType'=>$calculationType,'licenceType'=>$licenceType,
                    'entityType'=>$entityType,'regDate'=>$regDate,'accountCode'=>$accountCode]);


            }else{
                //no fee record found
                return response()->json(['success'=>12,'message'=>'No fee record was found']);
            }


        }else{
            //rhe item ID is not available,no reference found
            return response()->json(['success'=>2,'message'=>'The item ID is not available,no reference found']);
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

        $sysConfig = SystemConfig::invoiceGeneration();

        if ($sysConfig->invoiceGeneration = 1){
            $tempStatus = 2;
        }else{
            $tempStatus = 1;
        }

        return view('assessment.assessment.new_assessment')->with('title','New assessment')
            ->with('fee_accounts',$fee_accounts)->with('divisions',$divisions)->with('payment_id',$payment_id)->with(compact('tempStatus'));
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

    public function filterGeneratedAssessments(Request $request){

        $flag = $request->flag;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $searchType = $request->searchType;

        if ($searchType == 'assessment'){
            $payments = Payment::getAssessmentRecords($flag,$fromDate,$toDate);
            $flag = ucfirst($flag);

            if (strtolower($flag) == 'tmp'){
                $view = "temporary_assessments";
            }else{
                $view = "generated_assessments";
            }
        }else{
            $payments = Payment::getReceiptRecords($flag,$fromDate,$toDate);
            $view = "generated_receipts";
        }


        return view('assessment.assessment.'.$view)->with('title',$flag)->with(compact('payments','flag'));


    }

    public function generatedReceipts($flag){

        $payments = Payment::getReceiptRecords($flag);
        $flag = ucfirst($flag);

        return view('assessment.assessment.generated_receipts')->with('title',$flag)->with(compact('payments','flag'));

    }

    public function generatedAssessments($flag){

        $payments = Payment::getAssessmentRecords($flag);
        $flag = ucfirst($flag);

        if (strtolower($flag) == 'tmp'){
            return view('assessment.assessment.temporary_assessments')->with('title',$flag)->with(compact('payments','flag'));
        }else{
            return view('assessment.assessment.generated_assessments')->with('title',$flag)->with(compact('payments','flag'));

        }

    }

}
