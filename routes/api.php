<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function (){
    Route::middleware('auth:sanctum')->name('transactions.')->group(function () {
        Route::get('transactions', [TransactionController::class, 'index'])->name('index');
        Route::post('transactions', [TransactionController::class, 'store'])->name('store');
        Route::get('transactions/{reference_number}', [TransactionController::class, 'show'])->name('show');
    });
});
