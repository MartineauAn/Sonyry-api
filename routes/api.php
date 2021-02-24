<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategorieController;
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
    route::put('topics/{id}/update',[\App\Http\Controllers\API\TopicController::class,'update']);
    route::delete('topics/{id}/destroy',[\App\Http\Controllers\API\TopicController::class,'destroy']);

    /**
     * CATEGORIES ROUTES
     */
    Route::get('categorie/create',[CategorieController::class,'create']);
    Route::post('categorie/store', [CategorieController::class, 'store']);
    Route::get('categorie/index', [CategorieController::class, 'index']);
    Route::get('categorie/{id}/destroy',[CategorieController::class, 'destroy']);

});


