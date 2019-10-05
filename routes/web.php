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


Route::get('/images/raw', 'ImageController@listRaw');
Route::get('/images/{image}', 'ImageController@get')->where('image', '\d+');
Route::get('/images/raw/search/{query}', 'ImageController@searchRaw')->where('query', '.*');
Route::post('/images/raw', 'ImageController@storeRaw');
Route::delete('/images/{id}', 'ImageController@deleteRaw')->where('id', '\d+');


/**
 * Users
 */
Route::get('/users/{user}', 'UserController@show')
     ->where('user', '\d+')
     ->middleware('can:manage,user');

Route::get('/users', 'UserController@index')
     ->middleware('can:list,App\User');

Route::put('/users/{user}', 'UserController@update')
     ->where('user', '\d+')
     ->middleware('can:manage,user');

Route::delete('/users/{user}', 'UserController@destroy')
     ->where('user', '\d+')
     ->middleware('can:manage,user');

Route::post('/users', 'UserController@store')
     ->middleware('can:create,App\User');


/**
 * Defaults routes
 *
 * @todo: clean up
 */
Route::get('/', function () {
    return view('welcome');
});
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
