<?php

use App\Http\Controllers\CandidatesController;
use App\Http\Controllers\ClassesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ElectionSessionsController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::apiResource('election-sessions', ElectionSessionsController::class);
Route::apiResource('candidates', CandidatesController::class);
Route::apiResource('classes', ClassesController::class);
