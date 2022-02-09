<?php

namespace App\Http\Controllers\Assessment;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use function redirect;
use function response;
use function view;

class UserController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function searchUser(Request $request){

        try {

        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'BusinessNameController','bn_updates_applications','business-name-app-error');
            return redirect()->to('bn/new-updates')->with('error-message',$message);
        }

    }
    public function search_employee(Request $request){
        $Employee_Name=Input::get('Employee_Name');
        $employees = \App\User::where('name', 'LIKE', '%'.$Employee_Name.'%')->get();

        return view('assessment.admin.search_employee_result')->with('employees', $employees)->with('title', 'Employees');
    }

    public function enableUser(Request $request){
        $id = $request->id;

        if($id != null){

            $user = User::findOrFail($id);

            if (!empty($user)){
                $user->account_status = 1;
                $user->save();
            }

            return response()->json(['success'=>1,'message'=>'User successfully activated']);

        }else{
            redirect()->route('registered')->with('error-message', 'No employee with such an ID,try another one.');
        }


    }

    public function removeUser(Request $request){
        $id = $request->id;

        if($id != null){

            $user = User::findOrFail($id);

            if (!empty($user)){
                $user->account_status = 0;
                $user->save();
            }

            return response()->json(['success'=>1,'message'=>'User successfully disabled']);

        }else{
            redirect()->route('registered')->with('error-message', 'No employee with such an ID,try another one.');
        }


    }
    public function users(){
        $users = User::all();
        $users = User::paginate(10);
        return view('assessment.admin.users')->with('title', 'Users')->with(compact('users'));
    }
}
