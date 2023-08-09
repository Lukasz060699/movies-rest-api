<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::get('/movies', 'App\Http\Controllers\MovieController@index');
Route::post('/movies', 'App\Http\Controllers\MovieController@store');
Route::get('/movies/search', 'App\Http\Controllers\MovieController@searchByTitle');
Route::get('/movies/{id}', 'App\Http\Controllers\MovieController@show');
Route::put('/movies/{id}', 'App\Http\Controllers\MovieController@update');
Route::delete('/movies/{id}', 'App\Http\Controllers\MovieController@destroy');
Route::post('/genres', 'App\Http\Controllers\GenreController@store');