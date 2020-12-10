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
Route::post('/send-review', 'ProductController@reviewSubmit');
Route::get('/get-product-review', 'ProductController@getProductReview');
Route::get('/get-review', 'ProductController@getReview');
Route::get('/get-cupone', 'CartController@getCupone');
Route::post('/update-cart', 'CartController@updateCart');
Route::get('/change-currency', 'HomeController@changeCurrency');
Route::get('/add-to-whish', 'ProductController@addToWish');

/*Route::group(['middleware' => ['']], function () {
    Route::post('/update-cart', 'CartController@updateCart');
});*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
