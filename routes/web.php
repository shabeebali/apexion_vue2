<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::middleware('auth')->group(function(){
<<<<<<< HEAD
	Route::get('/', function () {
	    return view('backend.dashboard');
	});
=======
	Route::get('/{any}', function () {
    	return view('backend.default');
	})->where('any', '.*');
>>>>>>> origin/master
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
