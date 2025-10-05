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

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::middleware(['auth:sanctum', 'role:admin'])->apiResource('election-sessions', ElectionSessionsController::class);
Route::middleware(['auth:sanctum', 'role:admin'])->apiResource('candidates', CandidatesController::class);
Route::middleware(['auth:sanctum', 'role:admin'])->apiResource('classes', ClassesController::class);
Route::middleware(['auth:sanctum', 'role:admin'])->apiResource('participants', ParticipantsController::class);
Route::middleware(['auth:sanctum', 'role:admin'])->apiResource('users', UserController::class);

Route::middleware(['auth:sanctum', 'role:voter management'])->prefix('checkin')->group(function () {
    Route::get('active-device', [BarcodeCheckinController::class, 'listActiveDevice']);
    Route::get('generate', [BarcodeCheckinController::class, 'generateDeviceId']);
    Route::get('{deviceId}', [BarcodeCheckinController::class, 'generateBarcodeCheckin']);
});


Route::middleware(['auth:sanctum', 'role:voter management'])->prefix('ballot-box')->group(function () {
    Route::get('active-device/{deviceId?}', [BarcodeBallotBoxController::class, 'listActiveDevice']);
    Route::get('generate', [BarcodeBallotBoxController::class, 'generateDeviceId']);
    Route::get('{deviceId}', [BarcodeBallotBoxController::class, 'generateBarcodeCheckin']);
});


Route::middleware(['auth:sanctum', 'role:voter'])->prefix('scan')->group(function () {
    Route::post('checkin', [ScanController::class, 'scanCheckin']);
    Route::post('ballot-box', [ScanController::class, 'scanBallotBox']);
});

Route::middleware(['auth:sanctum', 'role:voter management'])->post('votes', [VotesController::class, 'store']);

Route::group(['prefix' => 'quick-count'], function () {
    Route::get('/', [QuickCount::class, 'AllCount']);
    Route::get('byclass', [QuickCount::class, 'CountByClass']);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::get('logout', [AuthController::class, 'logut']);
    });
});
