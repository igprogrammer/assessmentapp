<?php

namespace App\Http\Controllers\Assessment;



use App\Http\Controllers\Controller;
use App\Models\Assessment\Currency;
use App\Models\Assessment\Division;
use App\Models\Assessment\EventLog;
use App\Models\Assessment\Fee;
use App\Models\Assessment\FeeAccount;
use App\Models\Assessment\FeeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use function decrypt;
use function encrypt;
use function redirect;
use function response;
use function view;

class FeeController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function updateFeeAccount(Request $request){
        try {
            $division_id = $request->division_id;
            $account_code = $request->account_code;
            $account_name = $request->account_name;
            $group_number = $request->group_number;
            $feeAccountId = $request->feeAccountId;

            $validator = Validator::make($request->all(),FeeAccount::$add_rules);
            if($validator->passes()){
                $check_if_exists = FeeAccount::find($feeAccountId);
                if (!empty($check_if_exists)){

                    $feeAccount = FeeAccount::updateFeeAccount($feeAccountId,$division_id,$account_code,$account_name,$group_number);
                    if (!empty($feeAccount)){
                        $message = Auth::user()->name.' Successfully updated fee account';
                    }else{
                        $message =  Auth::user()->name.' Failed to update fee account';
                    }

                    Log::channel('fee')->info($message);
                    EventLog::saveEvent(Auth::user()->username,'System access','User', Auth::user()->name,'Success','Update fee account',
                        $message,EventLog::getIpAddress(),EventLog::getMacAddress(),'FeeController','updateFeeAccount');


                    return redirect()->back()->with('title','Update fee account')->with('success-message','Fee account successfully updated');
                }else{
                    return redirect()->back()->with('error-message','No Fee account exists');
                }
            }else{
                return redirect()->back()->with('title','Edit fee account')->with('error-message','Please fill the following:')->withInput()->withErrors($validator);
            }
        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'FeeController','updateFeeAccount','fee-error');
            return redirect()->back()->with('error-message',$message);
        }
    }

    public function editFeeAccount($feeAccountId){
        try {

            $feeAccountId = decrypt($feeAccountId);
            $divisions = array();
            foreach (Division::all() as $division){
                $divisions[$division->id] = $division->division_name;
            }

            if (empty($feeAccountId)){
                return redirect()->back()->with('error-message','No reference provided');
            }

            $feeAccount = FeeAccount::find($feeAccountId);
            if (empty($feeAccount)){
                return redirect()->back()->with('error-message','No record was found using the provided reference');
            }

            return view('assessment.fee_accounts.edit_fee_account')->with('title','Edit fee account')
                ->with(compact('feeAccount','divisions','division'));

        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'FeeController','saveFeeAccount','fee-error');
            return redirect()->back()->with('error-message',$message);
        }
    }

    public function saveFeeAccount(Request $request){
        try {

            $division_id = $request->division_id;
            $account_code = $request->account_code;
            $account_name = $request->account_name;
            $group_number = $request->group_number;

            $validator = Validator::make($request->all(),FeeAccount::$add_rules);
            if($validator->passes()){
                $check_if_exists = FeeAccount::where(['account_code'=>$account_code,'division_id'=>$division_id])->first();
                if (empty($check_if_exists)){

                    $feeAccount = FeeAccount::saveFeeAccount($division_id,$account_code,$account_name,$group_number);
                    if (!empty($feeAccount)){
                        $message = Auth::user()->name.' Successfully added new fee account';
                    }else{
                        $message =  Auth::user()->name.' Failed to add new fee account';
                    }

                    Log::channel('fee')->info($message);
                    EventLog::saveEvent(Auth::user()->username,'System access','User', Auth::user()->name,'Success','Add fee account',
                        $message,EventLog::getIpAddress(),EventLog::getMacAddress(),'FeeController','saveFeeAccount');


                    return redirect()->back()->with('title','Add fee account')->with('success-message','New fee account successfully added');
                }else{
                    return redirect()->back()->with('error-message','Fee account already exists');
                }
            }else{
                return redirect()->back()->with('title','Add fee account')->with('error-message','Please fill the following:')->withInput()->withErrors($validator);
            }

        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'FeeController','saveFeeAccount','fee-error');
            return redirect()->back()->with('error-message',$message);
        }
    }

    public function addFeeAccount(){
        $divisions = array();
        foreach (Division::all() as $division){
            $divisions[$division->id] = $division->division_name;
        }
        $title = 'Add fee account';
        return view('assessment.fee_accounts.add_fee_account')->with(compact('divisions','title'));
    }

    public function feeAccounts(){
        $fee_accounts = FeeAccount::all();
        return view('assessment.fee_accounts.fee_accounts')->with('title','Fee accounts')->with(compact('fee_accounts'));
    }

    public function updateFeeItem(Request $request){
        try {

            $fee_id = $request->fee_id;
            $item_name = $request->item_name;
            $item_amount = $request->item_amount;
            $penalty_amount = $request->penalty_amount;
            $days = $request->days;
            $copy_charge = $request->copy_charge;
            $stamp_duty_amount = $request->stamp_duty_amount;
            $currency = $request->currency;
            $feeItemId = $request->feeItemId;

            $validator = Validator::make($request->all(), FeeItem::$add_rules);

            if($validator->passes()){
                $check = FeeItem::find($feeItemId);
                if (!empty($check)){

                    $feeItem = FeeItem::updateFeeItem($feeItemId,$fee_id,$item_name,$item_amount,$penalty_amount,$days,$copy_charge,$stamp_duty_amount,$currency);

                    if (!empty($feeItem)){
                        $message = Auth::user()->name.' Successfully updated fee item';
                    }else{
                        $message =  Auth::user()->name.' Failed to  update fee item';
                    }

                    Log::channel('fee')->info($message);
                    EventLog::saveEvent(Auth::user()->username,'System access','User', Auth::user()->name,'Success','Update fee item',
                        $message,EventLog::getIpAddress(),EventLog::getMacAddress(),'FeeController','updateFeeItem');

                    return redirect()->back()->with('title','Update fee item')->with('success-message','Successfully updated fee items');
                }else{
                    return redirect()->back()->with('error-message','No record to update was found');
                }
            }else{
                return redirect()->to('fees/fee-item/'.encrypt($feeItemId).'/edit')->with('title','Fees')->with('error-message','Please fill the following:')->withInput()->withErrors($validator);
            }


        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'FeeController','updateFeeItem','fee-error');
            return redirect()->back()->with('error-message',$message);
        }
    }

    public function editFeeItem($feeItemId,$flag){
        try {
            $feeItemId = decrypt($feeItemId);
            $feeItem = FeeItem::find($feeItemId);

            if (!empty($feeItem)){
                $fee = Fee::find($feeItem->fee_id);
            }

            $divisions = array();
            foreach (Division::all() as $division){
                $divisions[$division->id] = $division->division_name;
            }

            $currencies = array();
            foreach (Currency::all() as $currency){
                $currencies[$currency->code] = $currency->name;
            }

            $feeAccount = FeeAccount::find($fee->fee_account_id);
            $division = Division::find($feeAccount->division_id);
            $feeAccounts = array();
            foreach (FeeAccount::where(['division_id'=>$division->id])->get() as $feeAcc){
                $feeAccounts[$feeAcc->id] = $feeAcc->account_name;
            }

            $fees = array();
            foreach (Fee::where(['fee_account_id'=>$fee->fee_account_id,'active'=>'yes'])->get() as $item){
                $fees[$item->id] = $item->fee_name;
            }

            return view('assessment.fees.edit_fee_item')->with('title','Edit fee item')
                ->with(compact('fee','divisions','currencies','division','feeAccounts','feeItem','fees'));
        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'FeeController','editFeeItem','fee-error');
            return redirect()->back()->with('error-message',$message);
        }
    }

    public function updateFee(Request $request){
        try {

            $fee_account_id = $request->fee_account_id;
            $fee_code = $request->fee_code;
            $fee_name = $request->fee_name;
            $account_code = $request->account_code;
            $type = $request->type;
            $gfs_code = $request->gfs_code;
            $has_form = $request->has_form;
            $amount = $request->amount;
            $feeId = $request->feeId;
            $isActive = $request->active;

            $validator = Validator::make($request->all(), Fee::$add_rules);

            if($validator->passes()){
                $check = Fee::find($feeId);
                if (!empty($check)){

                    $fee = Fee::updateFee($feeId,$fee_account_id,$fee_name,$fee_code,$account_code,$amount,$has_form,$type,$gfs_code,$isActive);
                    if (!empty($fee)){
                        $message = Auth::user()->name.' Successfully updated fee';
                    }else{
                        $message =  Auth::user()->name.' Failed to  update fee';
                    }

                    Log::channel('fee')->info($message);
                    EventLog::saveEvent(Auth::user()->username,'System access','User', Auth::user()->name,'Success','Update fee',
                        $message,EventLog::getIpAddress(),EventLog::getMacAddress(),'FeeController','updateFee');

                    return redirect()->back()->with('title','Update fee')->with('success-message','Successfully updated fee');
                }else{
                    return redirect()->back()->with('error-message','No record to update was found');
                }
            }else{
                return redirect()->to('fees/edit-fee/'.encrypt($feeId))->with('title','Fees')->with('error-message','Please fill the following:')->withInput()->withErrors($validator);
            }


        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'FeeController','updateFee','fee-error');
            return redirect()->back()->with('error-message',$message);
        }
    }

    public function editFee($feeId){
        try {
            $feeId = decrypt($feeId);
            $fee = Fee::find($feeId);

            $divisions = array();
            foreach (Division::all() as $division){
                $divisions[$division->id] = $division->division_name;
            }

            $currencies = array();
            foreach (Currency::all() as $currency){
                $currencies[$currency->code] = $currency->name;
            }

            $feeAccount = FeeAccount::find($fee->fee_account_id);
            $division = Division::find($feeAccount->division_id);
            $feeAccounts = array();
            foreach (FeeAccount::where(['division_id'=>$division->id])->get() as $feeAcc){
                $feeAccounts[$feeAcc->id] = $feeAcc->account_name;
            }

            return view('assessment.fees.edit_fee')->with('title','Edit fee')
                ->with(compact('fee','divisions','currencies','division','feeAccounts'));
        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'FeeController','editFee','fee-error');
            return redirect()->back()->with('error-message',$message);
        }
    }

    public function saveFeeItem(Request $request){
        try {
            $fee_id = $request->fee_id;
            $item_name = $request->item_name;
            $item_amount = $request->item_amount;
            $penalty_amount = $request->penalty_amount;
            $days = $request->days;
            $copy_charge = $request->copy_charge;
            $stamp_duty_amount = $request->stamp_duty_amount;
            $currency = $request->currency;

            $validator = Validator::make($request->all(), FeeItem::$add_rules);
            if($validator->passes()){
                $check_if_exists = FeeItem::where('item_name','LIKE','%'.$item_name.'%')->where(['fee_id'=>$fee_id,'item_amount'=>$item_amount])->first();
                if (empty($check_if_exists)){

                    $saveFeeItem = FeeItem::saveFeeItem($fee_id,$item_name,$item_amount,$penalty_amount,$days,$copy_charge,$stamp_duty_amount,$currency);

                    if (!empty($saveFeeItem)){
                        $message = Auth::user()->name.' Successfully added new fee item';
                    }else{
                        $message =  Auth::user()->name.' Failed to  add new fee item';
                    }

                    Log::channel('fee')->info($message);
                    EventLog::saveEvent(Auth::user()->username,'System access','User', Auth::user()->name,'Success','Add fee',
                        $message,EventLog::getIpAddress(),EventLog::getMacAddress(),'FeeController','saveFeeItem');

                    return redirect()->back()->with('title','Add fee item')->with('success-message','New fee item successfully added');
                }else{
                    return redirect()->back()->with('error-message','Fee item already exists');
                }
            }else{
                return redirect()->back()->with('title','Add fee item')->with('error-message','Please fill the following:')->withInput()->withErrors($validator);
            }
        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'FeeController','saveFeeItem','fee-error');
            return redirect()->back()->with('error-message',$message);
        }
    }

    //get fees
    public function getFees(Request $request){
        $fee_account_id = $request->fee_account_id;

        if (!empty($fee_account_id)){
            $fees = Fee::where(['fee_account_id'=>$fee_account_id,'active'=>'yes'])->get();
            return view('assessment.fees.get_fees')->with('title','Fees list')->with('fees',$fees);
        }else{
            return response()->json(['success'=>2]);
        }
    }

    public function addFeeItem(){
        $fee_accounts = array();
        foreach (FeeAccount::all() as $fee_account){
            $fee_accounts[$fee_account->id] = $fee_account->account_name;
        }

        $currencies = array();
        foreach (Currency::all() as $currency){
            $currencies[$currency->code] = $currency->name;
        }

        $divisions = array();
        foreach (Division::all() as $division){
            $divisions[$division->id] = $division->division_name;
        }

        return view('assessment.fees.add_fee_item')->with('title','Add fee item')
            ->with(compact('fee_accounts','divisions','currencies'));
    }

    public function feeItems(){
        $fee_items = FeeItem::paginate(100);
        return view('assessment.fees.fee_items')->with('title','Fee items')->with(compact('fee_items'));
    }

    //save fee
    public function saveFee(Request $request){

        try {
            $fee_account_id = $request->fee_account_id;
            $fee_code = $request->fee_code;
            $fee_name = $request->fee_name;
            $account_code = $request->account_code;
            $type = $request->type;
            $gfs_code = $request->gfs_code;
            $has_form = $request->has_form;
            $amount = $request->amount;

            $validator = Validator::make($request->all(), Fee::$add_rules);

            if($validator->passes()){
                $check_if_exists = Fee::where(['fee_code'=>$fee_code,'gfs_code'=>$gfs_code])->first();
                if (empty($check_if_exists)){

                    $fee = Fee::saveFee($fee_account_id,$fee_name,$fee_code,$account_code,$amount,$has_form,$type,$gfs_code);
                    if (!empty($fee)){
                        $message = Auth::user()->name.' Successfully added new fee';
                    }else{
                        $message =  Auth::user()->name.' Failed to  add new fee';
                    }

                    Log::channel('fee')->info($message);
                    EventLog::saveEvent(Auth::user()->username,'System access','User', Auth::user()->name,'Success','Add fee',
                        $message,EventLog::getIpAddress(),EventLog::getMacAddress(),'FeeController','saveFee');

                    return redirect()->back()->with('title','Add fee')->with('success-message','New fee successfully added');
                }else{
                    return redirect()->back()->with('error-message','Fee already exists');
                }
            }else{
                return redirect()->to('fees/add')->with('title','Add fee')->with('error-message','Please fill the following:')->withInput()->withErrors($validator);
            }
        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'FeeController','saveFee','fee-error');
            return redirect()->back()->with('error-message',$message);
        }
    }

    public function getFeeAccounts(Request $request){
        $division_id = $request->division_id;
        if (!empty($division_id)){
            $fee_accounts = FeeAccount::where(['division_id'=>$division_id])->get();
            return view('assessment.fee_accounts.get_fee_accounts')->with('title','Fee account list')->with(compact('fee_accounts'));
        }else{
            return response()->json(['success'=>2]);
        }
    }

    public function getCode(Request $request){
        $fee_account_id = $request->fee_account_id;
        if (!empty($fee_account_id)){
            $fee_account = FeeAccount::find($fee_account_id);
            $account_code = $fee_account->account_code ?? 'NIL';
            return response()->json(['account_code'=>$account_code,'success'=>1]);
        }else{
            return response()->json(['success'=>2]);
        }
    }
    //load form to add fees
    public function add_fee(){
        $fee_accounts = array();
        foreach (FeeAccount::all() as $fee_account){
            $fee_accounts[$fee_account->id] = $fee_account->account_name;
        }

        $currencies = array();
        foreach (Currency::all() as $currency){
            $currencies[$currency->code] = $currency->name;
        }

        $divisions = array();
        foreach (Division::all() as $division){
            $divisions[$division->id] = $division->division_name;
        }

        return view('assessment.fees.add_fee')->with('title','Add fee')
            ->with('fee_accounts',$fee_accounts)->with('divisions',$divisions)->with('currencies',$currencies);
    }

    public function fees(){
        $fees = Fee::all();
        return view('assessment.fees.fees')->with('title','Fees')->with('fees',$fees);
    }

}
