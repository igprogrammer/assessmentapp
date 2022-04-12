<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Assessment\GeneralController;
use App\Http\Controllers\Controller;
use App\Models\Assessment\EventLog;
use App\Models\Customer\Customer;
use Cassandra\Custom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public  function __construct(){
        $this->middleware('auth');
    }

    public function searchCustomer(Request $request){

        try {

            $customerName = $request->customerName;
            $customerNumber = $request->customerNumber;

            if (!empty($customerName)){
                $customers = Customer::where('customer_name', 'LIKE', '%'.$customerName.'%')->paginate();
            }elseif (!empty($customerNumber)){
                $customers = Customer::where('company_number', 'LIKE', '%'.$customerNumber.'%')->paginate();
            }else{

                $customers = Customer::paginate(100);
            }

            return view('assessment.admin.search_customer_result')->with(compact('customers'))->with('title', 'Customers');

        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'BusinessNameController','bn_updates_applications','business-name-app-error');
            return redirect()->to('users')->with('error-message',$message);
        }

    }

    public function updateCustomer(Request $request){
        try {

            $id = $request->id;
            $customerName = $request->customerName;
            $companyNumber = $request->companyNumber;
            $regDate = $request->regDate;
            $entityType = $request->entityType;


            $customer = Customer::find($id);
            if (!empty($customer)){
                $customer = Customer::updateCustomer($id,$companyNumber,$customerName,$entityType,$regDate);
                if ($customer){
                    $message = Auth::user()->name.' successfully updated '.$customerName.' information';
                    $status = 'Success';
                }else{
                    $message = Auth::user()->name.' failed to update '.$customerName.' information';
                    $status = 'Fail';
                }
            }else{
                $status = 'Fail';
                $message = "No record found using the provided response";
            }


            Log::channel('customer')->info($message);
            EventLog::saveEvent(Auth::user()->email,'System access','User', Auth::user()->name,$status,'Update customer',
                $message,EventLog::getIpAddress(),EventLog::getMacAddress(),'CustomerController','updateCustomer');

            if ($status == 'Success'){
                return redirect()->to('customers/list')->with('success-message',$message);
            }else{
                return redirect()->back()->with('error-message',$message);
            }



        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'CustomerController','updateCustomer','customer-error');
            return redirect()->back()->with('error-message',$message);
        }
    }

    public function editCustomer($id){
        try {

            $id = decrypt($id);
            if (empty($id)){
                return redirect()->back()->with('error-message','No reference was found');
            }else{
                $customer = Customer::find($id);
                return view('assessment.admin.edit_customer')->with('title','Edit customer')->with(compact('customer'));
            }

        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'CustomerController','editCustomer','customer-error');
            return redirect()->back()->with('error-message',$message);
        }
    }

    public function saveCustomer(Request $request){
        try {

            $customerName = $request->customerName;
            $companyNumber = $request->companyNumber;
            $regDate = $request->regDate;
            $entityType = $request->entityType;

            $check = Customer::checkCustomer($companyNumber,$entityType);
            if (!empty($check)){
                $message = $customerName." is already in the system";
                $status = 'Failed';
            }else{
                $customer = Customer::saveCustomer($companyNumber,$customerName,$entityType,$regDate);
                if ($customer){
                    $message = Auth::user()->name.' successfully added '.$customerName.' as a new a customer';
                    $status = 'Success';
                }else{
                    $message = Auth::user()->name.' failed to add '.$customerName.' as a new a customer';
                    $status = 'Fail';
                }

            }


            Log::channel('customer')->info($message);
            EventLog::saveEvent(Auth::user()->email,'System access','User', Auth::user()->name,$status,'Save customer',
                $message,EventLog::getIpAddress(),EventLog::getMacAddress(),'CustomerController','saveCustomer');

            if ($status == 'Success'){
                return redirect()->back()->with('success-message',$message);
            }else{
                return redirect()->back()->with('error-message',$message);
            }

        }catch (\Exception $exception){
            $message = "An error has occurred,please contact System administrator";
            GeneralController::exceptionHandler('Controller',$exception,'CustomerController','saveCustomer','customer-error');
            return redirect()->back()->with('error-message',$message);
        }
    }

    public function addCustomer(){
        return view('assessment.admin.add_customer')->with('title', 'Add customer');
    }

    public function customers(){
        $customers = Customer::all();
        $customers = Customer::paginate(100);
        return view('assessment.admin.customers')->with('title', 'Customers')->with(compact('customers'));
    }
}
