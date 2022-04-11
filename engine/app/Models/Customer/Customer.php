<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Customer extends Model
{
    use HasFactory;

    public static function saveCustomer($company_number,$customer_name,$entityType,$regDate){
        $customer = new Customer();
        $customer->company_number = $company_number;
        $customer->customer_name = ucwords(strtolower($customer_name));
        $customer->entityType = $entityType;
        $customer->regDate = $regDate;
        $customer->user_id = Auth::user()->id;
        $customer->save();

        return $customer;
    }

    public static function checkCustomer($company_number,$entityType){
        return Customer::where(['company_number'=>$company_number,'entityType'=>$entityType])->first();
    }
}
