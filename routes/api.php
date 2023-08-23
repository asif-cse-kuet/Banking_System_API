<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/users', [UserController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/', [TransactionController::class, 'index']);
Route::get('/deposit', [TransactionController::class, 'showDeposits']);
Route::get('/withdrawal', [TransactionController::class, 'showWithdrawals']);
Route::post('/deposit', [TransactionController::class, 'deposit']);
Route::post('/withdrawal', [TransactionController::class, 'withdrawal']);
