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
     * DASHBOARD
     */

    Route::apiResource('dashboard' , \App\Http\Controllers\API\DashboardController::class);

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


    /**
     * BLOCS ROUTES
     */
    Route::get('blocs/{id}',[\App\Http\Controllers\API\BlocController::class , 'index']);
    Route::put('blocs/{id}',[\App\Http\Controllers\API\BlocController::class , 'update']);
    Route::post('blocs/{id}',[\App\Http\Controllers\API\BlocController::class , 'store']);
    Route::delete('blocs/{id}',[\App\Http\Controllers\API\BlocController::class , 'destroy']);


    /**
     * PAGES ROUTES
     */

    Route::post('pages/{id}',[\App\Http\Controllers\API\PageController::class , 'update']);
    Route::get('pages/edit/{id}',[\App\Http\Controllers\API\PageController::class , 'edit']);
    Route::apiResource('pages',\App\Http\Controllers\API\PageController::class);

    /**
     * COLLECTIONS
     */


    Route::post('collections/{id}' , [\App\Http\Controllers\API\CollectionController::class , 'update']);
    Route::apiResource('collections', \App\Http\Controllers\API\CollectionController::class);

    /**
     * COLLECTION_PAGES ROUTES
     */

    Route::get('collectionPages/{id}', [\App\Http\Controllers\API\CollectionPageController::class , 'index']);
    Route::post('collectionPages/{id}', [\App\Http\Controllers\API\CollectionPageController::class , 'store']);
    Route::post('collectionPages/delete/{id}', [\App\Http\Controllers\API\CollectionPageController::class , 'destroy']);

    /**
     * GROUPS
     */

    Route::get('groups/exit/{id}' , [\App\Http\Controllers\API\GroupController::class , 'exit']);
    Route::get('groups/{id}/kick/{user_id}' , [\App\Http\Controllers\API\GroupController::class , 'kick']);
    Route::get('groups/{id}/invite/{user_id}' , [\App\Http\Controllers\API\GroupController::class , 'invite']);
    Route::get('groups/{id}/kick/{notificationId}' , [\App\Http\Controllers\API\GroupController::class , 'accept']);
    Route::apiResource('groups' , \App\Http\Controllers\API\GroupController::class);

    /**
     * SHARES
     */

    Route::get('shares/{id}' , [\App\Http\Controllers\API\ShareController::class , 'index']);
    Route::post('shares' , [\App\Http\Controllers\API\ShareController::class , 'storeDirectory']);
    Route::get('shares/directory/{id}' , [\App\Http\Controllers\API\ShareController::class , 'directory']);
    Route::delete('shares/{id}/directory/{groupId}' , [\App\Http\Controllers\API\ShareController::class , 'destroyDirectory']);
    Route::get('shares/links/{id}' , [\App\Http\Controllers\API\ShareController::class , 'links']);
    Route::get('shares/pages/{id}' , [\App\Http\Controllers\API\ShareController::class , 'pages']);
    Route::post('shares/pages' , [\App\Http\Controllers\API\ShareController::class , 'sharePage']);
    Route::delete('shares/{id}' , [\App\Http\Controllers\API\ShareController::class , 'destroyShare']);
});


