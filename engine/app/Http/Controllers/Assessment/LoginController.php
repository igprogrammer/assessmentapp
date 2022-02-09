<?php

namespace App\Http\Controllers\Assessment;

use App\Http\Controllers\Controller;
use App\Models\Assessment\AuditTrail;
use App\Models\Assessment\EventLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use function redirect;
use function view;

class LoginController extends Controller
{
    public function authenticate(Request $request){
        $email = $request->email;
        $password= $request->password;

        $validator = Validator::make($request->all(), User::$login_rules);

        if($validator->passes()){
            if(Auth::attempt(['email'=>$email, 'password'=>$password]))
            {
                if(Auth::user()->account_status == 1){
                    //save entry to audit trail
                    $audit_trail = new AuditTrail();
                    $audit_trail->user_id = Auth::user()->id;
                    $audit_trail->operation_type = 'Authentication';
                    $audit_trail->activity = Auth::user()->name.'-Successfully logged in the system';
                    $audit_trail->controller_name = 'AdminController';
                    $audit_trail->function_name='authenticate()';
                    $audit_trail->entry_date = date('Y-m-d');
                    $audit_trail->save();


                    $message = Auth::user()->name.' Successfully logged in';
                    Log::channel('authentication')->info($message);
                    EventLog::saveEvent($email,'System access','User', Auth::user()->name,'Success','Login',
                        $message,EventLog::getIpAddress(),EventLog::getMacAddress(),'LoginController','authenticate');
                    return redirect()->to('dashboard')->with('title', 'Dashboard');
                }else{
                    return redirect()->to('/')->with('error-message','Your account has been blocked.');
                }
            }else{
                return redirect()->to('/')->with('wrong-creds','Wrong username or password.')->withInput();
            }
        }else{
            return redirect()->to('/')->with('error-message', 'Ooops, some errors occurred.')->withInput()->withErrors($validator);
        }


    }

    public function login()
    {
        return view('auth.login')->with('title','Login');
    }

    public function logout(){
        Auth::logout();
        return redirect('/');
    }
}
