<?php

use Illuminate\Http\Request;
use App\Model\Country;
use App\Model\State;
use App\Model\City;
use App\User;
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
    if($request->with_permissions){
        $model = User::with('roles.permissions')->find($user->id);
        return $model->toArray();
    }
    return $user->toArray();
});
Route::middleware('auth:api')->group(function(){
    Route::get('places',function(Request $request){
        if($request->country_id){
            $states = State::where('country_id',$request->country_id)->get();
            $states->prepend(['id'=>0,'name'=>'--']);
            return $states;
        }
        if($request->state_id){
            $cities = City::where('state_id',$request->state_id)->get();
            return $cities->prepend(['id'=>0,'name'=>'--']);
        }
        $countries = Country::all();
        $phone_countries = $countries->map(function($item){
            return [
                'id' => $item->id,
                'name'=>$item->name.'('.$item->phonecode.')',
            ];
        });
        return [
            'countries' => $countries,
            'phone_countries' => $phone_countries
        ];
    });
    Route::post('products/add_comment/{id}','Backend\ProductController@add_comment');
    Route::post('products/import','Backend\ProductController@import');
    Route::post('products/remove_stock/{id}','Backend\ProductController@remove_stock');
    Route::post('products/delete_alias/{id}','Backend\ProductController@delete_alias');
    Route::post('products/delete_media/{id}','Backend\ProductController@delete_media');
    Route::post('customers/check/','Backend\CustomerController@check');
    Route::put('customers/check/{id}','Backend\CustomerController@check');
    Route::get('customers/add_search','Backend\CustomerController@addSearch');
    Route::get('customers/delete_address/{id}','Backend\CustomerController@deleteAddress');
    Route::get('products/notif_test','Backend\ProductController@notif_test');
    
    Route::get('menu','Backend\MenuController');
    Route::post('products/upload','Backend\ProductController@upload');
    Route::post('users/chpass/{id}','Backend\UserController@change_pass');
    Route::get('users/roles/permissions','Backend\UserRoleController@permissions');
    Route::get('categories/export','Backend\CategoryController@export');
    Route::post('categories/import','Backend\CategoryController@import');

    Route::get('customers/export','Backend\CustomerController@export');
    Route::post('customers/import','Backend\CustomerController@import');
    Route::resources([
        'users_roles'=>'Backend\UserRoleController',
        'users' => 'Backend\UserController',
        'pricelists' =>'Backend\PricelistController',
        'warehouses' =>'Backend\WarehouseController',
        'taxonomies' =>'Backend\TaxonomyController',
        'categories' =>'Backend\CategoryController',
        'products' =>'Backend\ProductController',
        'customers'=>'Backend\CustomerController',
        'sales'=>'Backend\SaleController',
    ]);
    
});