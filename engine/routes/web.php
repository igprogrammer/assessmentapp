<?php

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

Route::get('enable-user',[\App\Http\Controllers\Assessment\UserController::class,'enableUser'])->name('enable-user');
Route::get('search-user',[\App\Http\Controllers\Assessment\UserController::class,'searchUser'])->name('search-user');
Route::get('disable-user',[\App\Http\Controllers\Assessment\UserController::class,'removeUser'])->name('disable-user');
Route::get('users',[\App\Http\Controllers\Assessment\UserController::class,'users']);
Route::get('dashboard',[\App\Http\Controllers\Assessment\PrivateController::class,'dashboard']);
Route::post('authenticate',[\App\Http\Controllers\Assessment\LoginController::class,'authenticate']);
Route::get('/',[\App\Http\Controllers\Assessment\LoginController::class,'login']);
