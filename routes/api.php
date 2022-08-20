<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\NewsFeedController;
use App\Http\Controllers\Api\PromotionController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/reset_password', [AuthController::class, 'resetPassword']);

Route::group(['middleware' => 'auth-api'], function() {
    
    Route::group(['prefix' => 'room'], function() {
        Route::get('/',[RoomController::class, 'index']);
        Route::post('/create',[RoomController::class, 'create']);
        Route::post('/update',[RoomController::class, 'update']);
        Route::post('/delete',[RoomController::class, 'delete']);
        Route::get('/detail',[RoomController::class, 'detail']);
    });

    Route::group(['prefix' => 'reservation'], function() {
        Route::get('/',[RoomController::class, 'reservationList']);
        Route::post('/book',[RoomController::class, 'book']);
        Route::post('/cancel',[RoomController::class, 'cancelReservation']);
        Route::post('/confirm_payment',[RoomController::class, 'confirmPaymentReservation']);
    });

    Route::group(['prefix' => 'feed'], function() {
        Route::get('/',[NewsFeedController::class, 'index']);
        Route::post('/create',[NewsFeedController::class, 'create']);
        Route::post('/update',[NewsFeedController::class, 'update']);
        Route::post('/delete',[NewsFeedController::class, 'delete']);
        Route::get('/detail',[NewsFeedController::class, 'detail']);
    });

    Route::group(['prefix' => 'promotion'], function() {
        Route::get('/',[PromotionController::class, 'index']);
        Route::post('/create',[PromotionController::class, 'create']);
        Route::post('/update',[PromotionController::class, 'update']);
        Route::post('/delete',[PromotionController::class, 'delete']);
        Route::get('/detail',[PromotionController::class, 'detail']);
    });
});