<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\NewEntryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');



// Route::middleware('auth:sanctum')->group(function () {
//        Route::post('/logout', [AuthController::class, 'logout']);
// });
// Route::middleware('guest')->group(function () {
// Route::post('/login', [AuthController::class,'login']);
// });

Route::post('/register', [NewEntryController::class, 'register']);


Route::post('/fingerprints', [FingerprintController::class, 'store']);
Route::get('/fingerprints', [FingerprintController::class, 'index']);