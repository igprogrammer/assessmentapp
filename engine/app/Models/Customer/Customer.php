<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    public static function checkCustomer($company_number){
        return Customer::where('company_number','=',$company_number)->first();
    }
}
