<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Booking extends Model
{
    use HasFactory;

    public static function getBookingInfo($controlNo){
        return DB::connection('pgsql')->table('booking')->where(['invoice'=>$controlNo])->first();
    }
}
