<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\HomeController;


Route::get('/', function () {
    return redirect()->route('login');
});


Auth::routes();

Route::get('/home', [HomeController::class, 'index'])
    ->middleware('auth')
    ->name('home');

Route::middleware(['auth'])->group(function () {

    Route::resource('vendors', VendorController::class);

 
    Route::post('vendors/{id}/submit', [VendorController::class, 'submit'])->name('vendors.submit');
    Route::post('vendors/{id}/approve', [VendorController::class, 'approve'])->name('vendors.approve');
    Route::post('vendors/{id}/reject', [VendorController::class, 'reject'])->name('vendors.reject');
    Route::post('vendors/{id}/send-back', [VendorController::class, 'sendBack'])->name('vendors.sendBack');
});