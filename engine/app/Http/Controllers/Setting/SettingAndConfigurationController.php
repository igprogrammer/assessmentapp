<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Assessment\EventLog;
use App\Models\Billing\BillPayOption;
use App\Models\SystemConfig\SystemConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SettingAndConfigurationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function updatePayOption(Request $request){
        $id = $request->id;
        $billOpt = $request->BillPayOpt;
        if ($billOpt == 1){
            $optionName = 'Full';
        }elseif ($billOpt == 2){
            $optionName = 'Partial';
        }else{
            $optionName = 'Exact';
        }
        $option = BillPayOption::find($id);
        $option->BillPayOptName = $optionName;
        $option->BillPayOpt = $billOpt;
        $option->save();

        $message = Auth::user()->name.' Successfully updated bill payment option';
        Log::channel('pay-option')->info($message);
        EventLog::saveEvent(Auth::user()->username,'System access','User', Auth::user()->name,'Success','Update payment option',
            $message,EventLog::getIpAddress(),EventLog::getMacAddress(),'SettingAndConfigurationController','updatePayOption');
        return redirect()->to('settings/pay-options')->with('success-message',$message);
    }

    public function editPayOption($id){
        $id = decrypt($id);
        $option = BillPayOption::find($id);
        return view('assessment.settings.editPayOption')->with('title','Edit payment option')->with(compact('option'));
    }

    public function invoiceGenereation(){
        $options = SystemConfig::all();
        return view('assessment.settings.payOptions')->with('title','Payment options')->with(compact('options'));
    }

    public function payOptions(){
        $options = BillPayOption::all();
        return view('assessment.settings.payOptions')->with('title','Payment options')->with(compact('options'));
    }
}
