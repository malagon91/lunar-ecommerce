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


Route::get('/', [
	'uses' => '\Lunar\Http\Controllers\CatalogController@index',
	'as' => 'index',
]);

Route::get('/catalog', [
	'uses' => '\Lunar\Http\Controllers\CatalogController@greatdetail',
	'as' => 'great-detail',
]);

Route::get('/search', [
    'uses' => '\Lunar\Http\Controllers\SearchController@query',
    'as' => 'search.query',
]);

Route::get('/catalog/{id}', [
    'uses' => '\Lunar\Http\Controllers\CatalogController@detail',
    'as' => 'product.detail',
]);

/* Front-End Client */

Auth::routes();


Route::group(['middleware' => 'auth'], function(){

	/* Profile Overview */

	Route::get('/profile', [
		'uses' => '\Lunar\Http\Controllers\UserController@profile',
		'as' => 'profile.index',
	]);

	/* Orders Summary */

	Route::get('/profile/orders', [
		'uses' => '\Lunar\Http\Controllers\UserController@orders',
		'as' => 'profile.orders',
	]);

	/* Address Funcionality */

	Route::get('/profile/addresses', [
		'uses' => '\Lunar\Http\Controllers\UserController@addresses',
		'as' => 'profile.address.index',
	]);

	Route::get('/profile/addresses/new', [
		'uses' => '\Lunar\Http\Controllers\UserController@createAddress',
		'as' => 'profile.address.create',
	]);

	Route::post('/profile/addresses/new', [
		'uses' => '\Lunar\Http\Controllers\UserController@storeAddress',
		'as' => 'profile.address.store',
	]);


	/* Wishlist */

	Route::get('/profile/wishlist', [
		'uses' => '\Lunar\Http\Controllers\UserController@wishlist',
		'as' => 'profile.wishlist',
	]);



	/* Checkout Process */
	Route::get('/checkout',[
		'uses' => 'CatalogController@checkout',
		'as' => 'checkout',
	]);

	Route::post('/checkout',[
		'uses' => 'CatalogController@postCheckout',
		'as' => 'checkout',
	]);


});

/* Shopping Cart */

Route::get('/cart/{id}',[
	'uses' => 'CatalogController@addCart',
	'as' => 'add-cart',
]);

Route::get('/cart',[
	'uses' => 'CatalogController@cart',
	'as' => 'cart',
]);

Route::get('/substract/{id}',[
	'uses' => 'CatalogController@removeOne',
	'as' => 'cart.substract',
]);

/* Admin Dashboard */

Route::group(['middleware' => 'guest:admin'], function(){
	Route::get('/admin/login', [
		'uses' => '\Lunar\Http\Controllers\Admin\AuthController@login',
		'as' => 'admin.login',
	]);

	Route::post('/admin/login', [
		'uses' => '\Lunar\Http\Controllers\Admin\AuthController@postLogin',
		'as' => 'admin.login'
	]);	


});

/* MIDDLEWARE AUTH */

Route::group(['middleware' => 'auth:admin'], function(){

	Route::get('/admin/register-admin', [
		'uses' => '\Lunar\Http\Controllers\Admin\AuthController@register',
		'as' => 'admin.register',
	]);

	Route::post('/admin/register-admin', [
		'uses' => '\Lunar\Http\Controllers\Admin\AuthController@postRegister',
		'as' => 'admin.register',
	]);
	
	Route::get('/admin', [
		'uses' => '\Lunar\Http\Controllers\Admin\DashboardController@dashboard',
		'as' => 'admin.dashboard'
	]);

	//Route::resource('/admin/productos', 'Admin\ProductoController');

});