<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\RatingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
*/
// User authentication 
Route::post('/login', [AuthController::class, 'login']);

// Routes protected by the "auth:sanctum" middleware (for user and admin)
Route::middleware('auth:sanctum')->group(function (){
    Route::get('/movies', [MovieController::class, 'index']);
    Route::get('/movies/search', [MovieController::class, 'searchByTitle']);
    Route::get('/movies/{id}', [MovieController::class, 'show']);
    Route::post('movies/{id}/rate', [RatingController::class, 'store']);
    Route::delete('movies/{id}/rate', [RatingController::class, 'destroy']);    
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Routes protected by both "auth:sanctum" and "admin" middleware (just admin)
Route::middleware(['auth:sanctum', 'admin'])->group(function (){
    Route::post('/movies', [MovieController::class, 'store']);
    Route::put('/movies/{id}', [MovieController::class, 'update']);
    Route::delete('/movies/{id}', [MovieController::class, 'destroy']);
    Route::post('/genres', [GenreController::class, 'store']);
    Route::delete('/genres/{id}', [GenreController::class, 'destroy']);
});