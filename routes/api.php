<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategorieController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\ProfilController;
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
    Route::get('categorie/index', [CategorieController::class,'index']);
    Route::get('categorie/create',[CategorieController::class,'create']);
    Route::post('categorie/store', [CategorieController::class,'store']);
    Route::delete('categorie/{id}/destroy',[CategorieController::class,'destroy']);


    /**
     * BLOCS ROUTES
     */
    Route::get('blocs/{id}',[\App\Http\Controllers\API\BlocController::class , 'index']);
    Route::put('blocs/{id}',[\App\Http\Controllers\API\BlocController::class , 'update']);
    Route::post('blocs/{id}',[\App\Http\Controllers\API\BlocController::class , 'store']);
    //Route::apiResource('blocs',\App\Http\Controllers\API\BlocController::class);

    /**
     * COMMENTS ROUTES
     */
    Route::post('/comments/{id}/store', [CommentController::class,'store']);
    Route::post('/commentReply/{id}/storeCommentReply', [CommentController::class,'storeCommentReply']);

    /**
     * PROFIL ROUTES

    Route::get('/profil',[ProfilController::class,'index']);
    Route::put('/profil/{id}/update',[ProfilController::class,'update']);
    Route::get('/profil/group/{id}/exit',[UserGroupController::class,'destroy']);
    Route::get('/profil/friend/{id}/destroy',[FriendController::class,'destroy']);
    Route::get('/profil/friend/{id}/add', [FriendController::class,'add']);
    Route::get('/profil/friend/{id}/request',[FriendController::class,'request']);
     */

});


