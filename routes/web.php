<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\HomeController;

// Root → login
Route::get('/', function () {
    return redirect()->route('login');
});

// Laravel UI auth routes (login, register, etc.)
Auth::routes([
    'reset'    => false, // spec says no forgot password
    'verify'   => false,
    'register' => true,
]);

// Authenticated routes
Route::middleware('auth')->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // CRUD — no destroy (spec doesn't require delete)
    Route::resource('vendors', VendorController::class)
         ->except(['destroy']);

    // Status transition actions
    Route::post('vendors/{vendor}/submit',    [VendorController::class, 'submit'])   ->name('vendors.submit');
    Route::post('vendors/{vendor}/approve',   [VendorController::class, 'approve'])  ->name('vendors.approve');
    Route::post('vendors/{vendor}/reject',    [VendorController::class, 'reject'])   ->name('vendors.reject');
    Route::post('vendors/{vendor}/send-back', [VendorController::class, 'sendBack']) ->name('vendors.send_back');
});