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
Route::group(['prefix'=>'assessments'], function (){
    Route::post('request-control-number',[\App\Http\Controllers\Billing\BillingController::class,'requestControlNumber']);
    Route::get('/calculate-fee',[\App\Http\Controllers\Assessment\FeeCalculationController::class,'calculateFee'])->name('calculate-fee');
    Route::get('/search-assessment',[\App\Http\Controllers\Assessment\AssessmentController::class,'searchAssessment'])->name('search-assessment');
    Route::post('/filter',[\App\Http\Controllers\Assessment\AssessmentController::class,'filterAssessment'])->name('filter');
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
    Route::get('/print-assessment',[\App\Http\Controllers\Assessment\AssessmentController::class,'printAssessment'])->name('print-assessment');
    Route::get('/list/{flag}',[\App\Http\Controllers\Assessment\AssessmentController::class,'generatedAssessments'])->name('/individual/{flag}');
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
