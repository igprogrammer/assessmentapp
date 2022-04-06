<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class IncomingControlNumber extends Model
{
    use HasFactory;

    public static function updateIncomingControlNumberMessage($bookingId, $message){
        DB::table('incoming_control_numbers')->where(['billId'=>$bookingId])->update(array(
            'responseMessage'=>$message
        ));
    }

    public static function saveIncomingControlNumber($xml,$message,$bookingId){
        $data = new IncomingControlNumber();
        $data->xmlContent = $xml;
        $data->billId = $bookingId;
        $data->receiveMessage = $message;
        $data->save();
    }

    public static function getIncomingControlNumberInfo($bookingId){

        return DB::table('incoming_control_numbers')->where(['billId'=>$bookingId])->first();

    }
}
