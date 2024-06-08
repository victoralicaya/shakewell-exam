<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [UserController::class, 'register'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/vouchers', [VoucherController::class, 'index'])->name('vouchers');
    Route::post('/voucher', [VoucherController::class, 'store'])->name('voucher');
    Route::delete('/voucher/{voucher}', [VoucherController::class, 'destroy'])->name('voucher');
});
