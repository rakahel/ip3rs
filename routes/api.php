<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\TechnicianController;

use App\Components\Data\Pagination;
use App\Models\Log;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
| -------------------------------------------------------------------------
| Token, JWT, Passport or Sanctum ?? Which one is the best for app authentication?
|
| 1. Passport : Passport provides a full OAuth2 server implementation for your
| Laravel application in a matter of minutes. It is therefore necessary to have
| a brief knowledge of OAuth2.
|
| 2. Sanctum : Sanctum it is a simple package to issue API tokens to your users
| without the complication of OAuth. Sanctum uses Laravel's built-in cookie
| based session authentication services.
|
| In a small application use Sanctum. it's simple and easy
|
| 3. JWT : Auth (Authentication) is the process of identifying the user
| credentials. In web applications, authentication is managed by sessions which
| take the input parameters such as email or username and password, for user
| identification. If these parameters match, the user is said to be authenticated.
| -------------------------------------------------------------------------
|
*/

Route::get('/', function() {
    $model = Log::orderBy('id', 'asc');
    $pg = new Pagination($model);
    $result = [
        //'data' => $pg->getData(),
        'pagination' => $pg->getPagination()
    ];
    return response()->json($result, 200);





    // print('<pre>'.print_r(\Illuminate\Log\LogServiceProvider::pathsToPublish()).'</pre>');exit;
    // print('<pre>'.print_r(request()->get('page', 1),true).'</pre>');exit;

    //$model = new Log();
    //$ref = new ReflectionClass($model);
    //print('<pre>'.print_r(get_class($model),true).'</pre>');exit;
    //print('<pre>'.print_r(get_parent_class($model),true).'</pre>');exit;
    //return response()->json($model->limit(10)->offset(10)->get(), 200);
    //$pagination = new Pagination();
});


/*
| ------------------------------------------------------------------------
| AuthController
| ------------------------------------------------------------------------
*/
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user/profile', [AuthController::class, 'userProfile']);
});

/*
| ------------------------------------------------------------------------
| ProductController
| ------------------------------------------------------------------------
*/
Route::get('/product/{search?}/{value?}', [ProductController::class, 'search']);

/*
| ------------------------------------------------------------------------
| ScheduleController
| ------------------------------------------------------------------------
*/
Route::get('/schedule/{search?}/{value?}', [ScheduleController::class, 'search']);
Route::put('/schedule/{id}/status', [ScheduleController::class, 'changeStatus']);
Route::post('/schedule', [ScheduleController::class, 'store']);

/*
| ------------------------------------------------------------------------
| LocationController
| ------------------------------------------------------------------------
*/
Route::get('/location/{search?}/{value?}', [LocationController::class, 'search']);

/*
| ------------------------------------------------------------------------
| RoomController
| ------------------------------------------------------------------------
*/
Route::get('/room/{search?}/{value?}', [RoomController::class, 'search']);


/*
| ------------------------------------------------------------------------
| TechnicianController
| ------------------------------------------------------------------------
*/
Route::get('/technician/{search?}/{value?}', [TechnicianController::class, 'search']);

?>
