<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();

});

//Routes du TP2 ici : 
Route::group(['middleware' => ['throttle:5,1']], function () {
    Route::post('/signup', 'App\Http\Controllers\AuthController@register');
    Route::post('/signin', 'App\Http\Controllers\AuthController@login');
    Route::post('/signout', 'App\Http\Controllers\AuthController@logout')->middleware('auth:sanctum');
});

Route::group(['middleware' => ['throttle:60,1']], function () { 
    Route::group(['middleware'=>['auth:sanctum']], function() {
        Route::group(['middleware'=>['adminmiddleware']], function() {
            Route::post('/films', 'App\Http\Controllers\FilmController@create');
            Route::put('/films/{id}', 'App\Http\Controllers\FilmController@update');
            Route::delete('/films/{id}', 'App\Http\Controllers\FilmController@destroy');
        });
        
        Route::group(['middleware'=>['onecriticperfilmmiddleware']], function() {
            Route::post('/critics', 'App\Http\Controllers\CriticController@create');
        });
    
        Route::group(['middleware'=>['sameusermiddleware']], function() {
            Route::get('/users/{id}', 'App\Http\Controllers\UserController@show');
            Route::patch('/users/{id}', 'App\Http\Controllers\UserController@edit');
        });
        
    });
});
