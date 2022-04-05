<?php

namespace App\Models\ExchangeRate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;

    public static function getExchangeRate(){
        return ExchangeRate::first();
    }
}
