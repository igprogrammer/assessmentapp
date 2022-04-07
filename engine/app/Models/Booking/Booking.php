<?php

namespace App\Models\Booking;

use App\Models\SendGepgContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Booking extends Model
{
    use HasFactory;

    public static function createBooking($bookingId,$reference){
        $booking = Booking::where(['reference'=>$reference,'bookingId'=>$bookingId])->first();
        if (empty($booking)){
            $bookInfo = new Booking();
            $bookInfo->bookingId = $bookingId;
            $bookInfo->reference = $reference;
            $bookInfo->save();
        }else{
            $bookInfo = $booking;
        }

        return $bookInfo;
    }

    public static function incrementBookingId($bookingId){
        DB::table('bookings')->where([]);
    }

    public static function getLastBooking(){
        $id = Booking::max('id');
        if ($id != null){
            $booking = Booking::find($id);
            if (!empty($booking)){
                $bookingId = $booking->bookingId;
            }else{
                $bookingId = 0;
            }
        }else{
            $bookingId = 0;
        }

        return $bookingId;

    }

    public static function saveBillContent($bookingId,$billContent){

        $check = DB::table('send_gepg_contents')->where(['billId'=>$bookingId])->first();
        if (empty($check)){

            $bill = new SendGepgContent();
            $bill->billId = $bookingId;
            $bill->xmlContent = $billContent;
            $bill->save();
        }else{
            $bill = SendGepgContent::find($check->id);
            $bill->xmlContent = $billContent;
            $bill->save();
        }

    }

    public static function getBookingInfoByReference($reference){
        return  DB::connection('pgsql')->table('booking')->where(['reference'=>$reference])->first();

    }

    public static function getBookingInfoById($bookingId){
        return  DB::connection('pgsql')->table('booking')->where(['booking_id'=>$bookingId])->first();

    }

    /*public static function getInvoiceName($booking_id){
        return DB::connection('pgsql')->table('brela_invoice')->select()->where('booking_id',$booking_id)->first();
    }*/

    public static function getInvoiceName($bookingId){
        return DB::table('customers')->select()->where('booking_id',$booking_id)->first();
    }

    public static function getBookingData($invoice){
        return DB::table('payments')->select()->where('reference',$invoice)->first();
    }

    public static function saveBrelaInvoice($booking_id,$company_name){
        $data = DB::connection('pgsql')->table('brela_invoice')->where(['booking_id'=>$booking_id])->first();
        if (empty($data)){
            DB::connection('pgsql')->table('brela_invoice')->insert(array('booking_id' => $booking_id,
                'name' => $company_name));
        }else{
            return $data;
        }

    }

    public static function saveOBRSInvoice($total_amount,$invoice,$curr,$sc,$comma_separated,$flag,$phone_number,$exchange_rate,$expire_days,$expire_date,$billType,$bankName){

        $data = DB::connection('pgsql')->table('booking')->where(['reference'=>$invoice])->first();
        if (empty($data)){
            DB::connection('pgsql')->table('booking')->insert(array('amount' => $total_amount,
                'invoice' => $invoice,
                'currency'=>$curr,
                'section_id'=>$sc,
                'summary'=>$comma_separated,
                'booking_from'=>$flag,
                're_assessment_from'=>$sc,
                'reference'=>$invoice,
                'phone_number'=>$phone_number,
                'exchange_rate'=>$exchange_rate,
                'bl_exchange_rate'=>$exchange_rate,
                'expire_days'=>$expire_days,
                'expire_date'=>$expire_date,
                'billtype'=>$billType,
                'transferbank'=>$bankName));
        }else{
            return $data;
        }



    }

    public static function getBookingInfo($controlNo){
        return DB::connection('pgsql')->table('booking')->where(['invoice'=>$controlNo])->first();
    }
}
