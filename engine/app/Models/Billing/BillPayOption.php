<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillPayOption extends Model
{
    use HasFactory;

    public static function getBillPayOpt(){
        return BillPayOption::first();
    }
}
