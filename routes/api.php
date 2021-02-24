<?php

use App\Http\Controllers\API\AuthController;
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

Route::get('/', function (){
   return \App\Models\User::all();
});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class,'login']);
    Route::post('logout', [AuthController::class,'logout']);
    Route::post('refresh', [AuthController::class,'refresh']);
    Route::post('me', [AuthController::class,'me']);

});


Route::middleware('jwt.auth')->group(function (){

    /**
     *TOPIC ROUTES
     */
    route::get('topics',[\App\Http\Controllers\API\TopicController::class,'index']);
    route::get('topics/create',[\App\Http\Controllers\API\TopicController::class,'create']);
    route::post('topics/store',[\App\Http\Controllers\API\TopicController::class,'store']);
    route::get('topics/{id}/show',[\App\Http\Controllers\API\TopicController::class,'show']);
    route::get('topics/{id}/edit',[\App\Http\Controllers\API\TopicController::class,'edit']);


    Route::get('blocs/{id}',[\App\Http\Controllers\API\BlocController::class , 'index']);
    Route::post('blocs/{id}',[\App\Http\Controllers\API\BlocController::class , 'store']);
    //Route::apiResource('blocs',\App\Http\Controllers\API\BlocController::class);

});


