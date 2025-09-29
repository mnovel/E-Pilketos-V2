<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ElectionSessionsController;
use App\Http\Controllers\CandidatesController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\ParticipantsController;
use App\Http\Controllers\UserController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::apiResource('election-sessions', ElectionSessionsController::class);
Route::apiResource('candidates', CandidatesController::class);
Route::apiResource('classes', ClassesController::class);
Route::apiResource('participants', ParticipantsController::class);
Route::apiResource('users', UserController::class);
