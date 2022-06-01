<?php

use App\Http\Controllers\Billing\BillingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('xml', function (){
    $data = BillingController::generateBill(31250352,3059);
    dd($data);
});

Route::get('bill', function (){
    $data = BillingController::generateBill(31401730,9);
    dd($data);
});


Route::get('signed', function (){

    $d= \App\Models\SendGepgContent::first();
    $xml = $d->xmlContent;

    $data = BillingController::signedRequest($xml,3);
    dd($data);
});


Route::get('num', function (){
    $length = 10;
    $characters = '0123456789098765432101234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    $invoice = substr(time(),-1).substr(strtoupper($randomString), -5);
    return  $invoice;
});

Route::get('items', function (){

    $items = \App\Models\Assessment\Fee::all();

    $apps = array();
    foreach ($items as $item){

        $apps[] = $item->id;
        \Illuminate\Support\Facades\DB::connection('sqlsrv')->table('fee_items')->insert(array(
            'user_id'=>\Illuminate\Support\Facades\Auth::user()->id,
            'fee_id'=>$item->id,
            'item_name'=>$item->fee_name,
            'item_amount'=>$item->amount,
            'penalty_amount'=>2500,
            'days'=>14,
            'copy_charge'=>0,
            'stamp_duty_amount'=>0,
            'currency'=>$item->currency,
            'anniversary'=>'no',
            'active'=>'yes'
        ));


    }

    echo count($apps).' successfully';

});

Route::get('bls', function (){
    $types = \Illuminate\Support\Facades\DB::connection('mysql_b')->table('tnbp_eadvisory_category')
        ->where(['status'=>0,'licenseIssuingAuthority'=>'schedule A'])->get();

    $apps = array();
    foreach ($types as $type){

        $categoryName = $type->categoryName;
        $categoryName = simplexml_load_string($categoryName);
        $categoryName = json_encode($categoryName);
        $categoryName = json_decode($categoryName,TRUE);

        if (is_array($categoryName['CategoryName'])){
            $categoryName = $categoryName['CategoryName'][0];
        }else{
            $categoryName = $categoryName['CategoryName'];
        }

        $applyFeeInUsd = $type->applyFeeinUsd;
        $principalUsdFee = $type->licenseFeeUSD;
        $principalTzsFee = $type->licenseFeeTShs;
        $branchUsdFee = $type->branchLicenseFeeTShs;
        $branchTzsFee = $type->branchLicenseFeeUsd;
        $licenceType = $type->licenseIssuingAuthority;
        $isPerUnitFeeApplicable = $type->isPerUnitFeeApplicable;
        $perUnitlicenseFeeTShs = $type->perUnitlicenseFeeTShs;
        $perUnitlicenseFeeUSD = $type->perUnitlicenseFeeUSD;

        $checkName = \App\Models\Assessment\FeeItem::where(['item_name'=>$categoryName])->first();
        if (empty($checkName)){
            $apps[] = $type->categoryId;

            \Illuminate\Support\Facades\DB::table('fee_items')->insert(array(
                'user_id'=>\Illuminate\Support\Facades\Auth::user()->id,
                'fee_id'=>7,
                'item_name'=>$categoryName,
                'item_amount'=>$principalTzsFee,
                'penalty_amount'=>0,
                'days'=>28,
                'copy_charge'=>0,
                'stamp_duty_amount'=>0,
                'currency'=>'TShs',
                'anniversary'=>'no',
                'active'=>'yes',
                'applyFeeInUsd'=>$applyFeeInUsd,
                'principalUsdFee'=>$principalUsdFee,
                'principalTzsFee'=>$principalTzsFee,
                'branchUsdFee'=>$branchUsdFee,
                'branchTzsFee'=>$branchTzsFee,
                'licenceType'=>$licenceType,
                'isPerUnitFeeApplicable'=>$isPerUnitFeeApplicable,
                'perUnitlicenseFeeTShs'=>$perUnitlicenseFeeTShs,
                'perUnitlicenseFeeUSD'=>$perUnitlicenseFeeUSD
            ));

        }else{
            $apps[] = $type->categoryId;
            $categoryId = $type->categoryId;
            $checkName->categoryId = $categoryId;
            $checkName->save();
        }

    }

    dd(count($apps). ' items loaded');
});



Route::post('get-recon-data', array('as'=>'get-recon-data','uses'=>'PaymentController@receiveReconRequest'));
Route::post('receive-recon-request', array('as'=>'receive-recon-request','uses'=>'PaymentController@receiveReconRequest'));
Route::post('receive-bo-bill', array('as'=>'receive-bo-bill','uses'=>'PaymentController@receive_bo_bill'));
Route::post('receive-payment', array('as'=>'receive-payment','uses'=>'PaymentController@payment'));
Route::post('control-number-notification',[\App\Http\Controllers\BillPayment\BillAndPaymentController::class,'receiveBillRequestResponse']);


Route::group(['prefix'=>'settings'], function (){
    Route::post('/update-pay-option',[App\Http\Controllers\Setting\SettingAndConfigurationController::class,'updatePayOption'])->name('update-pay-option');
    Route::get('/pay-option/{id}/edit', [\App\Http\Controllers\Setting\SettingAndConfigurationController::class,'editPayOption'])->name('pay-option/{id}/edit');
    Route::get('/pay-options', [\App\Http\Controllers\Setting\SettingAndConfigurationController::class,'payOptions'])->name('pay-options');
});


Route::get('get-entity-data',[\App\Http\Controllers\Internal\ApplicationController::class,'getEntityData'])->name('get-entity-data');

Route::group(['prefix'=>'receipts'], function (){
    Route::get('/filter',[\App\Http\Controllers\Assessment\AssessmentController::class,'filterGeneratedAssessments']);
    Route::post('/filter',[\App\Http\Controllers\Assessment\AssessmentController::class,'filterGeneratedAssessments']);
    Route::get('/list/{flag}',[\App\Http\Controllers\Assessment\AssessmentController::class,'generatedReceipts'])->name('/list/{flag}');
});

Route::group(['prefix'=>'assessments'], function (){
    /*Route::get('req-cn',[BillingController::class,'reqCn'])->name('req-cn');*/
    Route::get('re-request-control-number',[\App\Http\Controllers\Billing\BillingController::class,'requestControlNumber'])->name('re-request-control-number');
    //Route::post('request-control-number',[\App\Http\Controllers\Billing\BillingController::class,'requestControlNumber']);
    Route::get('/calculate-fee',[\App\Http\Controllers\Assessment\FeeCalculationController::class,'calculateFee'])->name('calculate-fee');
    Route::get('/search-payment',[\App\Http\Controllers\Assessment\AssessmentController::class,'searchPayment'])->name('search-payment');
    Route::get('/search-assessment',[\App\Http\Controllers\Assessment\AssessmentController::class,'searchAssessment'])->name('search-assessment');
    Route::post('/filter',[\App\Http\Controllers\Assessment\AssessmentController::class,'filterGeneratedAssessments'])->name('filter');
    Route::get('/get-attachment/{attachment_id}',[\App\Http\Controllers\Assessment\AssessmentController::class,'getFileContent'])->name('get-attachment/{attachment_id}');
    Route::get('/assessment-items/{payment_id}/{flag}',[\App\Http\Controllers\Assessment\AssessmentController::class,'assessmentItems'])->name('assessment-items/{payment_id}/flag');
    Route::get('/print-bill-payment',[\App\Http\Controllers\Printing\PrintingController::class,'printBillPayment'])->name('print-bill-payment');
    Route::get('/print-assessment',[\App\Http\Controllers\Assessment\AssessmentController::class,'printAssessment'])->name('print-assessment');
    Route::get('/continue-assessment/{payment_id}',[\App\Http\Controllers\Assessment\AssessmentController::class,'continueAssessment'])->name('continue-assessment/{payment_id}');
    Route::get('/continue-assessment',[\App\Http\Controllers\Assessment\AssessmentController::class,'continueAssessment'])->name('continue-assessment');
    Route::post('/save-assessment',[\App\Http\Controllers\Assessment\AssessmentController::class,'saveAssessment']);
    Route::get('/temp-assessment-details/{tempAssId}',[\App\Http\Controllers\Assessment\AssessmentController::class,'pendingAssessmentDetails'])->name('temp-assessment-details/{tempAssId}');
    Route::get('/delete-assessment',[\App\Http\Controllers\Assessment\AssessmentController::class,'deleteAssessment'])->name('delete-assessment');
    Route::get('/pending',[\App\Http\Controllers\Assessment\AssessmentController::class,'pendingAssessment'])->name('pending');
    Route::get('remove-item',[\App\Http\Controllers\Assessment\AssessmentController::class,'removeItem'])->name('remove-item');
    Route::get('get-items',[\App\Http\Controllers\Assessment\AssessmentController::class,'getItems'])->name('get-items');
    Route::get('/check-fee',[\App\Http\Controllers\Assessment\AssessmentController::class,'checkFee'])->name('check-fee');
    Route::get('/display-fields',[\App\Http\Controllers\Assessment\AssessmentController::class,'displayFields'])->name('display-fields');
    Route::get('/add-assessment-fee',[\App\Http\Controllers\Assessment\AssessmentController::class,'addAssessmentFee'])->name('add-assessment-fee');
    Route::get('/get-selected-items',[\App\Http\Controllers\Assessment\AssessmentController::class,'getSelectedItems'])->name('get-selected-items');
    Route::get('/new-assessment',[\App\Http\Controllers\Assessment\AssessmentController::class,'newAssessment'])->name('new-assessment');
    Route::post('/print-assessment',[\App\Http\Controllers\Assessment\AssessmentController::class,'printAssessment'])->name('print-assessment');
    Route::get('/filter',[\App\Http\Controllers\Assessment\AssessmentController::class,'filterGeneratedAssessments'])->name('filter');
    Route::post('/filter',[\App\Http\Controllers\Assessment\AssessmentController::class,'filterGeneratedAssessments'])->name('filter');
    Route::get('/temp-filter', [\App\Http\Controllers\Assessment\AssessmentController::class,'filterGeneratedAssessments'])->name('temp-filter');
    Route::post('/temp-filter', [\App\Http\Controllers\Assessment\AssessmentController::class,'filterGeneratedAssessments'])->name('temp-filter');
    Route::get('/tmp/{flag}',[\App\Http\Controllers\Assessment\AssessmentController::class,'generatedAssessments'])->name('/tmp/{flag}');
    Route::get('/list/{flag}',[\App\Http\Controllers\Assessment\AssessmentController::class,'generatedAssessments'])->name('/list/{flag}');
});

Route::group(['prefix'=>'divisions'], function (){
    Route::post('/update-division',[\App\Http\Controllers\Assessment\DivisionController::class,'updateDivision']);
    Route::get('/edit-division/{divisionId}',[\App\Http\Controllers\Assessment\DivisionController::class,'editDivision'])->name('edit-division/{divisionId}');
    Route::post('/save-division',[\App\Http\Controllers\Assessment\DivisionController::class,'saveDivision']);
    Route::get('/add',[\App\Http\Controllers\Assessment\DivisionController::class,'addDivision']);
    Route::get('/list',[\App\Http\Controllers\Assessment\DivisionController::class,'divisions']);
});

Route::group(['prefix'=>'fees'], function (){
    Route::post('/update-fee-account',[\App\Http\Controllers\Assessment\FeeController::class,'updateFeeAccount'])->name('update-fee-account');
    Route::get('/edit-fee-account/{feeAccountId}',[\App\Http\Controllers\Assessment\FeeController::class,'editFeeAccount'])->name('edit-fee-account/{feeAccountId}');
    Route::post('/save-fee-account',[\App\Http\Controllers\Assessment\FeeController::class,'saveFeeAccount'])->name('save-fee-account');
    Route::get('/add-fee-account',[\App\Http\Controllers\Assessment\FeeController::class,'addFeeAccount'])->name('add-fee-account');
    Route::get('/fee-accounts',[\App\Http\Controllers\Assessment\FeeController::class,'feeAccounts'])->name('fee-accounts');
    Route::post('/update-fee-item',[\App\Http\Controllers\Assessment\FeeController::class,'updateFeeItem'])->name('update-fee-item');
    Route::get('/fee-item/{feeItemId}/{flag}',[\App\Http\Controllers\Assessment\FeeController::class,'editFeeItem'])->name('fee-item/{feeItemId}/{flag}');
    Route::post('/update-fee',[\App\Http\Controllers\Assessment\FeeController::class,'updateFee'])->name('update-fee');
    Route::get('/edit-fee/{feeId}',[\App\Http\Controllers\Assessment\FeeController::class,'editFee'])->name('edit-fee/{feeId}');
    Route::post('/save-fee-item',[\App\Http\Controllers\Assessment\FeeController::class,'saveFeeItem'])->name('save-fee-item');
    Route::get('/get-fees',[\App\Http\Controllers\Assessment\FeeController::class,'getFees'])->name('get-fees');
    Route::get('add-fee-item',[\App\Http\Controllers\Assessment\FeeController::class,'addFeeItem'])->name('add-fee-item');
    Route::get('/items',[\App\Http\Controllers\Assessment\FeeController::class,'feeItems'])->name('items');
    Route::post('/save-fee',[\App\Http\Controllers\Assessment\FeeController::class,'saveFee'])->name('save-fee');
    Route::get('/get-fee-accounts',[\App\Http\Controllers\Assessment\FeeController::class,'getFeeAccounts'])->name('get-fee-accounts');
    Route::get('/get-code',[\App\Http\Controllers\Assessment\FeeController::class,'getCode'])->name('get-code');
    Route::get('/add',[\App\Http\Controllers\Assessment\FeeController::class,'add_fee'])->name('add');
    Route::get('/list',[\App\Http\Controllers\Assessment\FeeController::class,'fees'])->name('list');
});

Route::group(['middleware' => ['auth']], function() {
    Route::get('logout',[\App\Http\Controllers\Assessment\LoginController::class,'login'])->name('logout');

});


Route::get('get-bl-calculation-criteria',[\App\Http\Controllers\Assessment\AssessmentController::class,'billCalculationCriteria'])->name('get-bl-calculation-criteria');
Route::post('change-password', [\App\Http\Controllers\Assessment\UserController::class,'updatePassword']);
Route::get('change-password/{employeeId}',[\App\Http\Controllers\Assessment\UserController::class,'changePassword']);
Route::post('update-user',[\App\Http\Controllers\Assessment\UserController::class,'updateUser']);
Route::get('user/{id}/edit',[\App\Http\Controllers\Assessment\UserController::class,'show'])->name('user/{id}/edit');
Route::post('save-user',[\App\Http\Controllers\Assessment\UserController::class,'saveUser']);
Route::get('register', [\App\Http\Controllers\Assessment\UserController::class,'register'])->name('register');
Route::get('enable-user',[\App\Http\Controllers\Assessment\UserController::class,'enableUser'])->name('enable-user');
Route::get('search-user',[\App\Http\Controllers\Assessment\UserController::class,'searchUser'])->name('search-user');
Route::get('disable-user',[\App\Http\Controllers\Assessment\UserController::class,'removeUser'])->name('disable-user');
Route::get('users',[\App\Http\Controllers\Assessment\UserController::class,'users']);
Route::get('dashboard',[\App\Http\Controllers\Assessment\PrivateController::class,'dashboard']);
Route::post('authenticate',[\App\Http\Controllers\Assessment\LoginController::class,'authenticate']);
Route::get('login',[\App\Http\Controllers\Assessment\LoginController::class,'login'])->name('login');
Route::get('/',[\App\Http\Controllers\Assessment\LoginController::class,'login']);

Route::group(['prefix'=>'customers'], function (){
    Route::get('search-customer', [\App\Http\Controllers\Customer\CustomerController::class,'searchCustomer'])->name('search-customer');
    Route::post('/update-customer',[\App\Http\Controllers\Customer\CustomerController::class,'updateCustomer']);
    Route::get('/edit/{id}',[\App\Http\Controllers\Customer\CustomerController::class,'editCustomer'])->name('/edit/{id}');
    Route::post('/save-customer',[\App\Http\Controllers\Customer\CustomerController::class,'saveCustomer']);
    Route::get('/add',[App\Http\Controllers\Customer\CustomerController::class,'addCustomer']);
    Route::get('/list',[App\Http\Controllers\Customer\CustomerController::class,'customers']);
});
