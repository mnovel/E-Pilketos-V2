<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ElectionSessionsController;
use App\Http\Controllers\CandidatesController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\ParticipantsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BarcodeCheckinController;
use App\Http\Controllers\BarcodeBallotBoxController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\VotesController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::apiResource('election-sessions', ElectionSessionsController::class);
Route::apiResource('candidates', CandidatesController::class);
Route::apiResource('classes', ClassesController::class);
Route::apiResource('participants', ParticipantsController::class);
Route::apiResource('users', UserController::class);

Route::group(['prefix' => 'checkin'], function () {
    Route::get('active-device', [BarcodeCheckinController::class, 'listActiveDevice']);
    Route::get('generate', [BarcodeCheckinController::class, 'generateDeviceId']);
    Route::get('{deviceId}', [BarcodeCheckinController::class, 'generateBarcodeCheckin']);
});

Route::group(['prefix' => 'ballot-box'], function () {
    Route::get('active-device/{deviceId?}', [BarcodeBallotBoxController::class, 'listActiveDevice']);
    Route::get('generate', [BarcodeBallotBoxController::class, 'generateDeviceId']);
    Route::get('{deviceId}', [BarcodeBallotBoxController::class, 'generateBarcodeCheckin']);
});


Route::group(['prefix' => 'scan'], function () {
    Route::post('checkin', [ScanController::class, 'scanCheckin']);
    Route::post('ballot-box', [ScanController::class, 'scanBallotBox']);
});

Route::get('votes', [VotesController::class, 'store']);
