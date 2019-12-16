<?php
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
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


Route::prefix('/admin')->group(function(){
	Auth::routes();
	Route::middleware('auth:web')->group(function(){
		Route::get('{folder?}/{file?}/{param?}',function(Request $request, $folder = null, $file= null, $param = null){
			$user = $request->user();
			if($folder != null){
				if($file != null){
					$filename = 'backend.'.$folder.'.'.$file;
				}
				else{
					$filename = 'backend.'.$folder.'.index';
				}
			}
			else{
				$filename = 'backend.dashboard.index';
			}
			if(View::exists($filename)){
				if($param){
					return view($filename,['prev_url'=> url()->previous(),'id'=>$param,'user'=>$user]);
				}
				return view($filename,['prev_url'=> url()->previous(),'user'=>$user]);
			}
			else abort(404);
		});
		/*
		Route::get('/', function () {
	    	return view('backend.default');
		});
		Route::get('/{any}', function () {
	    	return view('backend.default');
		})->where('any', '.*');
		*/
	});
});
Route::get('/home','HomeController@index');