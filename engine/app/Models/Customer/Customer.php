<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Customer extends Model
{
    use HasFactory;

    public static function saveCustomer($company_number,$customer_name){
        $customer = new Customer();
        $customer->company_number = $company_number;
        $customer->customer_name = ucwords(strtolower($customer_name));
        $customer->user_id = Auth::user()->id;
        $customer->save();

        return $customer;
    }

    public static function checkCustomer($company_number){
        return Customer::where('company_number','=',$company_number)->first();
    }
}
