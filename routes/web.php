<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ScheduleController;

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

Route::get('/', function () {
    return view('index');
});

// Api Flow
// GET:api/product/{id} => Api\ProductController@show
// GET:api/location => Api\LocationController@index
// GET:api/technician/search/{key?}/{value?} => Api\TechnicianController@search
// POST:api/schedule/store => Api\ScheduleController@store
Route::get('/schedule/create/{product_id}', [ScheduleController::class, 'create']);
