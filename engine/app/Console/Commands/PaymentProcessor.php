<?php

namespace App\Console\Commands;

use App\Models\IncomingPayment;
use App\Models\Payment\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentProcessor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'brela:payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is the command for processing payments for manual services';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            self::generate_receipt();
        }catch (\Exception $exception){
            $message = $exception->getMessage().' on line number '.$exception->getLine().' in file '.$exception->getFile();
            Log::channel('generate-receipt')->info($message);
        }
    }


    public function generate_receipt(){

        $incomingPayments = IncomingPayment::getPendingIncomingPayments();

        $counter = 0;
        if ($incomingPayments->isNotEmpty()){
            foreach($incomingPayments as $incomingPayment){

                $bookingId = $incomingPayment->billId;
                $xmlContent = $incomingPayment->xmlContent;
                $data = $xmlContent;
                $b_id = $incomingPayment->billId;

                $amount = $incomingPayment->PaidAmount;
                $currency = $incomingPayment->CCy;
                $paymentMethod = $incomingPayment->UsdPayChnl;
                $bankName = $incomingPayment->PspName;
                $bankAccountNumber = $incomingPayment->PspName;
                $payDate = $incomingPayment->TrxDtTm;
                $payDate = date('Y-m-d H:i:s',strtotime($payDate));
                $bankTransactionId = $incomingPayment->TrxId;
                $mobileTransactionId = $incomingPayment->TrxId;
                $phoneNumber = $incomingPayment->PyrCellNum;
                $PayRefId = $incomingPayment->PayRefId;
                $PspReceiptNumber = $incomingPayment->PspReceiptNumber;

                //$pay_date = date('Y-m-d H:i:s',strtotime($pay_date));
                $payMonth = date('m',strtotime($payDate));
                $payYear= date('Y',strtotime($payDate));

                $billInfo = Payment::getPaymentInfoByBookingId($bookingId);


                $b_id = $billInfo->bookingId;

                if(!empty($b_id)){
                    $checkPayment = Payment::checkPaymentCompleted($bookingId);

                    if(empty($checkPayment)){

                        if(empty($phoneNumber)){
                            $phoneNumber = $phoneNumber;
                        }else{
                            $phoneNumber = $billInfo->phone_number;
                        }

                        Payment::markPaymentAsReceived($bookingId,$amount,$payDate,$bankName,$phoneNumber,$bankTransactionId,$mobileTransactionId,$paymentMethod,$PayRefId,7101,$PspReceiptNumber,$payMonth,$payYear);

                        $paymentInfo = Payment::getPaymentInfoByBookingId($bookingId);

                        if (!empty($paymentInfo)){

                            if ((int)$paymentInfo->account_code == 440343){

                                $receiptInfo = DB::table('stamp_duty_receipt_number')->first();
                                $receiptNumber = $receiptInfo->receipt_number + 1;


                                Payment::updatePaymentReceiptNumber($bookingId,$receiptNumber);

                                DB::table('stamp_duty_receipt_number')->update(array(
                                    'receipt_number' => $receiptNumber
                                ));

                                sleep(2);

                                echo "Stamp Successfully Processed";

                            }else{

                                $receiptInfo = DB::table('normal_receipt_number')->first();
                                $receiptNumber = $receiptInfo->receipt_number + 1;


                                Payment::updatePaymentReceiptNumber($bookingId,$receiptNumber);

                                DB::table('normal_receipt_number')->update(array(
                                    'receipt_number' => $receiptNumber
                                ));

                                sleep(2);

                                echo "Stamp Successfully Processed";



                            }


                        }


                        DB::table('incoming_payments')->where(['billId'=>$bookingId])->update(array('status' => 1));//processed
                        $msg = 'Payment for control number '.$billInfo->invoice.' has been successfully processed and receipt number generated';
                        Log::channel('generate-receipt')->info($msg);
                    }else{
                        DB::table('incoming_payments')->where('billId', '=', $bookingId)->update(array('status' => 3));//already exists
                        $msg = 'The payment for control number '.$billInfo->invoice.' was already processed';
                        Log::channel('generate-receipt')->info($msg);
                        echo $msg;
                    }


                }else{

                    DB::table('incoming_payments')->where(['billId'=>$bookingId])->update(array('status' => '2'));//no record found
                    $msg = 'No booking record for control number '.$billInfo->invoice;
                    Log::channel('generate-receipt')->info($msg);
                    echo $msg;


                }

                sleep(1);

                $counter++;
            }
        }else{
            $msg = "No incoming payment(s) was found @ ".date('jS F, Y H:i:s');
            Log::channel('generate-receipt')->info($msg);
        }




    }
}
