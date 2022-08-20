<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('reset_password/{token}', [AuthController::class,'resetPassword']);
Route::post('reset_password', [AuthController::class,'changePassword']);
