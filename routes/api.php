<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ElectionSessionsController;
use App\Http\Controllers\CandidatesController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\ParticipantsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BarcodeCheckinController;
use App\Http\Controllers\BarcodeBallotBoxController;
use App\Http\Controllers\QuickCount;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\VotesController;


Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::get('me', [AuthController::class, 'me']);
    Route::get('logout', [AuthController::class, 'logout']);
});


Route::apiResource('election-sessions', ElectionSessionsController::class);
Route::apiResource('candidates', CandidatesController::class);
Route::apiResource('classes', ClassesController::class);
Route::apiResource('participants', ParticipantsController::class);
Route::apiResource('users', UserController::class);

Route::group(['prefix' => 'checkin'], function () {
    Route::get('active-device', [BarcodeCheckinController::class, 'listActiveDevice']);
    Route::get('generate', [BarcodeCheckinController::class, 'generateDeviceId']);
    Route::get('{deviceId}', [BarcodeCheckinController::class, 'generateBarcodeCheckin']);
    Route::delete('{deviceId}', [BarcodeCheckinController::class, 'deleteDeviceId']);
});

Route::group(['prefix' => 'ballot-box'], function () {
    Route::get('active-device/{deviceId?}', [BarcodeBallotBoxController::class, 'listActiveDevice']);
    Route::get('generate', [BarcodeBallotBoxController::class, 'generateDeviceId']);
    Route::get('{deviceId}', [BarcodeBallotBoxController::class, 'generateBarcodeBallotBox']);
    Route::delete('{deviceId}', [BarcodeBallotBoxController::class, 'deleteDeviceId']);
});

Route::group(['prefix' => 'scan'], function () {
    Route::post('checkin', [ScanController::class, 'scanCheckin']);
    Route::post('ballot-box', [ScanController::class, 'scanBallotBox']);
});

Route::post('votes', [VotesController::class, 'store']);

Route::group(['prefix' => 'quick-count'], function () {
    Route::get('/', [QuickCount::class, 'AllCount']);
    Route::get('byclass', [QuickCount::class, 'CountByClass']);
});
