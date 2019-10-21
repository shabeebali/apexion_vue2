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
    Route::get('menu','Backend\MenuController');
    Route::post('users/chpass/{id}','Backend\UserController@change_pass');
    Route::get('users/roles/permissions','Backend\UserRoleController@permissions');
    Route::get('categories/export','Backend\CategoryController@export');
    Route::post('categories/import','Backend\CategoryController@import');
    Route::resources([
        'users_roles'=>'Backend\UserRoleController',
        'users' => 'Backend\UserController',
        'pricelists' =>'Backend\PricelistController',
        'warehouses' =>'Backend\WarehouseController',
        'taxonomies' =>'Backend\TaxonomyController',
        'categories' =>'Backend\CategoryController',
        'products' =>'Backend\ProductController',
    ]);
    
});