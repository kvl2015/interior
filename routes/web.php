<?php

use Illuminate\Support\Facades\Route;

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


Route::get('admin/products/regenThumbnal', ['uses' => 'Admin\Voyager\ProductController@regenThumbnal',  'as' => 'voyager.products.regenThumbnal']);
Route::get('admin/products/export', ['uses' => 'Admin\Voyager\ProductController@export',  'as' => 'voyager.products.export']);
Route::get('admin/products/import', ['uses' => 'Admin\Voyager\ProductController@import',  'as' => 'voyager.products.import']);
Route::get('admin/products/add-options', ['uses' => 'Admin\Voyager\ProductController@addOptions',  'as' => 'voyager.products.addOptions']);
Route::get('admin/products/get-options', ['uses' => 'Admin\Voyager\ProductController@getOptions',  'as' => 'voyager.products.getOptions']);
Route::get('admin/products/get-groups', ['uses' => 'Admin\Voyager\ProductController@getGroups',  'as' => 'voyager.products.getGroups']);
Route::post('admin/main-categories/updateOrder/{id}', ['uses' => 'Admin\Voyager\MainCategoryController@updatePageCategory',  'as' => 'voyager.main-categories.updatePageOrder']);
Route::get('admin/preferences/addLookbook/{id}', ['uses' => 'Admin\Voyager\PreferenceController@addLookbook',  'as' => 'voyager.preferences.addLookbook']);
Route::get('admin/preferences/get-products', ['uses' => 'Admin\Voyager\PreferenceController@getProducts',  'as' => 'voyager.preferences.getProducts']);
Route::post('admin/preferences/updateLook/{id}', ['uses' => 'Admin\Voyager\PreferenceController@updateLook',  'as' => 'voyager.preferences.updateLook']);

Route::group(['middleware' => ['web']], function () {
    Route::post('/update-cart', 'CartController@updateCart');
    Route::post('/get-cupone', 'CartController@getCupone');
    Route::get('/change-currency', 'HomeController@changeCurrency');
});

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Route::get('/facebook/redirect', 'SocialAuthFacebookController@redirect');
Route::get('/facebook/callback', 'SocialAuthFacebookController@callback');

Route::group([
    'prefix' => '{locale}', 
    'where' => ['locale' => '[a-zA-Z]{2}_[a-zA-Z]{2}'], 
    'middleware' => 'setlocale'], function() {

    Auth::routes();
    //Auth::routes(['verify' => true]);

    Route::get('/', 'PageController@getMain')->name('page.main');
    Route::get('page/{slug}', 'PageController@viewPageStatic')->name('page.view');
    Route::get('brands', 'BrandController@view')->name('brand.view');
    Route::get('designers', 'DesignerController@view')->name('designer.view');
    Route::get('visited', 'ProductController@getVisited')->name('visited.view');
    Route::get('checkout/cart', 'CartController@view')->name('cart.view');
    Route::get('checkout/success', 'CartController@success')->name('cart.success');
    Route::get('checkout/cancel', 'CartController@cancel')->name('cart.cancel');
    Route::get('checkout/failure', 'CartController@failure')->name('cart.failure');
    Route::get('checkout/cart', 'CartController@view')->name('cart.view');
    Route::get('checkout', 'CartController@checkout')->name('cart.checkout');
    Route::get('/brand/{slug}', 'ProductController@getBrand')->name('product.brand');
    Route::get('/designer/{slug}', 'ProductController@getDesigner')->name('product.designer');
    Route::get('/style/{slug}', 'ProductController@getStyle')->name('product.style');
    Route::get('room', 'ProductController@getRoom')->name('designer.rooms');
    Route::get('/room/{slug}', 'ProductController@getRoom')->name('product.room');
    Route::get('/profile', 'ProfileController@index')->name('profile')->middleware('verified');
    Route::get('/{categorySlug}', 'ProductController@getCategoryStatic')->name('product.category');
});

Auth::routes(['verify' => true]);
//Auth::routes();

Route::get('/', 'PageController@getMain')->name('page.main');
Route::get('page/{slug}', 'PageController@viewPageStatic')->name('page.view');
Route::get('brands', 'BrandController@view')->name('brand.view');
Route::get('/profile', 'ProfileController@index')->name('profile')->middleware('verified');
Route::get('designers', 'DesignerController@view')->name('designer.view');
Route::get('visited', 'ProductController@getVisited')->name('visited.view');
Route::get('checkout/cart', 'CartController@view')->name('cart.view');
Route::get('checkout/success', 'CartController@success')->name('cart.success');
Route::get('checkout/cancel', 'CartController@cancel')->name('cart.cancel');
Route::get('checkout/failure', 'CartController@failure')->name('cart.failure');
Route::get('checkout/cart', 'CartController@view')->name('cart.view');
Route::get('checkout', 'CartController@checkout')->name('cart.checkout');
Route::get('/brand/{slug}', 'ProductController@getBrand')->name('product.brand');
Route::get('/designer/{slug}', 'ProductController@getDesigner')->name('product.designer');
Route::get('/style/{slug}', 'ProductController@getStyle')->name('product.style');
Route::get('room', 'ProductController@getRoom')->name('designer.rooms');
Route::get('/room/{slug}', 'ProductController@getRoom')->name('product.room');
Route::get('/{categorySlug}', 'ProductController@getCategoryStatic')->name('product.category');