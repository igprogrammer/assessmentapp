<?php

namespace App\Http\Controllers\Assessment;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use function redirect;
use function response;
use function view;

class UserController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    //function to process update password
    public function update_password(Request $request){
        $validator = Validator::make($request->all(), User::$update_password_rules);
        $employee_id = $request->id;


        if($validator->passes()){
            $employee = User::find($employee_id);
            $employee->password = Hash::make($request->password);

            $employee->save();

            return redirect()->back()->with('success-message', 'Password changed,use it during your next login.');
        }

        return redirect()->back()->with('error-message','Failed to update.')->withInput()->withErrors($validator);
    }

    //function to update password
    public function changePassword($employeeId = null){
        try {
            $employeeId = decrypt($employeeId);
            $employee = User::find($employeeId);
            return view('assessment.admin.change_password_form')->with('title', 'Change password')->with(compact('employee'));
        }catch (\Exception $exception){
            return redirect()->to('registered')->with('error-message',$exception->getMessage());
        }
    }

    public function updateUser(Request $request)
    {

        try {
            $id = $request->id;

            if($id != null){

                $validator = Validator::make($request->all(), User::$edit_rules);
                if($validator->passes()){

                    $user = User::find($id);
                    $user->name = $request->name;
                    $user->email = $request->email;
                    $user->username = $request->email;
                    $user->role = $request->role;
                    $user->isSupervisor = $request->isSupervisor;
                    $user->save();

                    $users = User::paginate(10);

                    return redirect()->to('users')->with('users', $users)->with('success-message','Employee information successfully updated.')->with('title', 'Employees');
                }else{
                    return redirect()->to('user/'.$id.'/edit')->with('error-message','Failed to update employee information.')->withInput()->withErrors($validator);
                }

            }else{
                redirect()->to('users')->with('error-message', 'No employee with the reference provided.');
            }

        }catch (\Exception $exception){
            $message = 'An error has occurred, please contact system administrator';
            GeneralController::exceptionHandler('Controller',$exception,'UserController','updateUser','user-error');
            return redirect()->back()->with('error-message',$message);
        }




    }

    public function show($id)
    {
        $id = decrypt($id);
        $user = User::find($id);
        return view('assessment.admin.edit_user')->with('title', 'Edit user')
            ->with('user', $user);
    }

    public function saveUser(Request $request)
    {
        $validator = Validator::make($request->all(), User::$add_rules);

        if($validator->passes()){
            $checkUser = User::where('name','LIKE','%'.$request->name.'%')->first();
            if (empty($checkUser)){

                $employee = new User();
                $employee->name = $request->name;
                $employee->username = strtolower($request->username);
                $employee->email = strtolower($request->username);
                $employee->password = Hash::make($request->password);
                $employee->role = $request->role;
                $employee->save();

            }

            return redirect()->to('register')->with('success-message', 'New user successfully added.');
        }

        return redirect()->to('register')->with('error-message','Failed to add new user.')->withInput()->withErrors($validator);
    }

    public function register()
    {
        return view('assessment.admin.register')->with('title', 'Register user');
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
