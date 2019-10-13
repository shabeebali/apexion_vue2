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
    return $user->hasRole('Super Admin') ? 'true' : 'null';
});
Route::middleware('auth:api')->group(function(){
	Route::get('users/list',function(){
		return response()->json([
            'items' => [
            	[
            		'id'=>'1',
            		'name'=>'shabeeb',
            		'email'=>'olakka'
            	]
            ]
        ]);
	});
	Route::put('users/create','Backend\UserController@store');
});