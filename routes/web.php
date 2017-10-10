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
	'uses' => '\Lunar\Http\Controllers\HomeController@index',
	'as' => 'index',
]);

/* Front-End Client */

Auth::routes();

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

	Route::get('/admin/register-admin', [
		'uses' => '\Lunar\Http\Controllers\Admin\AuthController@register',
		'as' => 'admin.register',
	]);

	Route::post('/admin/register-admin', [
		'uses' => '\Lunar\Http\Controllers\Admin\AuthController@postRegister',
		'as' => 'admin.register',
	]);
});

/* MIDDLEWARE AUTH */

Route::group(['middleware' => 'auth:admin'], function(){

	Route::get('/admin', [
		'uses' => '\Lunar\Http\Controllers\Admin\DashboardController@dashboard',
		'as' => 'admin.dashboard'
	]);

	//Route::resource('/admin/productos', 'Admin\ProductoController');

});