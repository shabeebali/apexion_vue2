<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    $user = $request->user();
    return $user->toArray();
});
Route::middleware('auth:api')->group(function(){
    Route::resources([
        'users_roles'=>'Backend\UserRoleController',
        'users' => 'Backend\UserController'
    ]);
    Route::post('users/chpass/{id}','Backend\UserController@change_pass');
    Route::get('users/roles/permissions','Backend\UserRoleController@permissions');
});