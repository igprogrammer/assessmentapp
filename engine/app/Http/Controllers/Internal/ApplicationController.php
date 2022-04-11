<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\Entity;
use Cassandra\Custom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    //function to get entity data
    public function getEntityData(Request $request){
        try {


            $companyNumber = $request->companyNumber;
            $entityType = $request->entityType;
            $type = $entityType;


            if ($entityType == 'CMP'){
                $entityType = 'ET-COMPANY';
            }elseif ($entityType == 'BN'){
                $entityType = 'ET-BUSINESS';
            }

            $local = DB::table('customers')->where(['company_number'=>$companyNumber,'entityType'=>$type])->first();
            //$data = DB::connection('sqlsrv_orsreg')->table('REG_LENTITY')->where(['company_type'=>$entityType,'CERT_NUMBER'=>$companyNumber])->first();
            //$imported = DB::connection('sqlsrv_orsreg')->table('REG_NAME_IMPORTED')->where([/*'company_type'=>$entityType,*/ 'REG_NUMBER'=>$companyNumber])->whereIn('SOURCE',array('RIMS1','SAPERION-C'))->first();
            //$appList = DB::connection('sqlsrv_ors')->table('E_APPLICATION')->where(['STATE_ID'=>'SAVED','CERT_NUMBER'=>$companyNumber,'OBJECT_TYPE'=>strtoupper($type)])->first();
            if (!empty($local)){
                $name = $local->customer_name;
                $date = $local->regDate;

                return response()->json(['success'=>1,'name'=>$name,'date'=>$date]);

            }elseif (!empty($data)){
                $name = $data->legal_name;
                $date = null;

                if ($entityType == 'ET-COMPANY'){
                    $date = $data->incorporation_date ?? $data->reg_date;
                }elseif ($entityType == 'ET-BUSINESS'){
                    $date = $data->reg_date ?? $data->incorporation_date;
                }

                return response()->json(['success'=>1,'name'=>$name,'date'=>$date]);

            }elseif (!empty($appList)){

                $name = $appList->ENTITY_NAME;
                $date = null;

                if ($entityType == 'ET-COMPANY'){
                    $date = $appList->REGISTER_DATE ?? date('Y-m-d');
                }elseif ($entityType == 'ET-BUSINESS'){
                    $date = $appList->REGISTER_DATE ?? date('Y-m-d');
                }

                return response()->json(['success'=>1,'name'=>$name,'date'=>$date]);

            }elseif (!empty($imported)){

                $name = $imported->NAME;
                $date = null;

                if ($entityType == 'ET-COMPANY'){
                    $date = $imported->REG_DATE ?? date('Y-m-d');
                }elseif ($entityType == 'ET-BUSINESS'){
                    $date = $imported->REG_DATE ?? date('Y-m-d');
                }

                return response()->json(['success'=>1,'name'=>$name,'date'=>$date,]);

            }else{
                return response()->json(['success'=>0,'name'=>'','date'=>'']);
            }

        }catch (\Exception $exception){
            $message = $exception->getMessage().' on line '.$exception->getLine().' of file '.$exception->getFile();
            Log::channel('assessment')->error($message);
        }
    }
}
