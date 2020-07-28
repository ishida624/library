<?php

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::middleware('login')->apiResource('/BorrowLog', 'api\LogController'); 
Route::post('/user/login', 'api\UserController@login');
Route::middleware('login')->apiResource('/library', 'api\BookController');
Route::middleware('login')->apiResource('/user', 'api\UserController');
Route::middleware('login')->post('/borrow', 'api\LogController@borrow');
Route::middleware('login')->put('/returnBook', 'api\LogController@returnBook');
Route::middleware('login')->get('/BorrowLog', 'api\LogController@index');
Route::middleware('login')->get('/BorrowLog/user/{UserId}', 'api\LogController@UserBorrowLog');
Route::middleware('login')->get('/BorrowLog/book/{BookId}', 'api\LogController@BookBorrowLog');
