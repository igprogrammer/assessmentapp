<?php

namespace App\Http\Controllers\Assessment;

use App\Http\Controllers\Controller;
use App\Models\Assessment\Division;
use App\Models\Assessment\EventLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use function redirect;
use function view;

class DivisionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function updateDivision(Request $request){
        try {
            $division_code = $request->division_code;
            $division_name = $request->division_name;
            $description = $request->description;
            $divisionId = $request->divisionId;

            $validator = Validator::make($request->all(), Division::$add_rules);

            if($validator->passes()){
                $check_if_exists = Division::find($divisionId);
                if (!empty($check_if_exists)){

                    $division = Division::updateDivision($divisionId,$division_code,$division_name,$description);
                    if (!empty($division)){
                        $message = Auth::user()->name.' Successfully updated division';
                    }else{
                        $message =  Auth::user()->name.' Failed to  update division';
                    }

                    Log::channel('division')->info($message);
                    EventLog::saveEvent(Auth::user()->username,'System access','User', Auth::user()->name,'Success','Update division',
                        $message,EventLog::getIpAddress(),EventLog::getMacAddress(),'DivisionController','updateDivision');

                    return redirect()->back()->with('title','Add division')->with('success-message','Division successfully updated');
                }else{
                    return redirect()->back()->with('error-message','No division exists');
                }
            }else{
                return redirect()->to('divisions/edit-division/'.encrypt($divisionId))->with('title','Add fee')->with('error-message','Please fill the following:')->withInput()->withErrors($validator);
            }


        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'DivisionController','updateDivision','division-error');
            return redirect()->back()->with('error-message',$message);
        }
    }

    public function editDivision($divisionId){
        try {

            $divisionId = decrypt($divisionId);
            if (empty($divisionId)){
                return redirect()->back()->with('error-message','No reference provided');
            }

            $division = Division::find($divisionId);
            if (empty($division)){
                return redirect()->back()->with('error-message','No record was found using the reference provided');
            }

            return view('assessment.divisions.edit_division')->with(compact('division'))->with('title','Edit division');

        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'DivisionController','editDivision','division-error');
            return redirect()->back()->with('error-message',$message);
        }
    }

    public function saveDivision(Request $request){
        try {
            $division_code = $request->division_code;
            $division_name = $request->division_name;
            $description = $request->description;

            $validator = Validator::make($request->all(), Division::$add_rules);

            if($validator->passes()){
                $check_if_exists = Division::where(['division_code'=>$division_code])->first();
                if (empty($check_if_exists)){

                    $division = Division::saveDivision($division_code,$division_name,$description);
                    if (!empty($division)){
                        $message = Auth::user()->name.' Successfully added new division';
                    }else{
                        $message =  Auth::user()->name.' Failed to  add new division';
                    }

                    Log::channel('division')->info($message);
                    EventLog::saveEvent(Auth::user()->username,'System access','User', Auth::user()->name,'Success','Add division',
                        $message,EventLog::getIpAddress(),EventLog::getMacAddress(),'DivisionController','saveDivision');

                    return redirect()->back()->with('title','Add division')->with('success-message','New division successfully added');
                }else{
                    return redirect()->back()->with('error-message','Division already exists');
                }
            }else{
                return redirect()->to('divisions/add')->with('title','Add fee')->with('error-message','Please fill the following:')->withInput()->withErrors($validator);
            }


        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'DivisionController','saveDivision','division-error');
            return redirect()->back()->with('error-message',$message);
        }
    }
    public function addDivision(){
        try {
            return view('assessment.divisions.add_division')->with('title','Add division');
        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'DivisionController','addDivision','division-error');
            return redirect()->back()->with('error-message',$message);
        }
    }

    public function divisions(){
        $divisions = Division::all();
        return view('assessment.divisions.divisions')->with('title','Divisions')->with('divisions',$divisions);
    }
}
