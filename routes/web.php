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
 * Roles
 */
Route::get('/users/{user}/roles/{role}', 'RoleController@show')
     ->where(['user' => '\d+', 'role' => '\d+'])
     ->middleware('can:view,role');

Route::get('/users/{user}/roles', 'RoleController@index')
     ->where('user', '\d+')
     ->middleware('can:view,App\Role');

Route::put('/users/{user}/roles/{role}', 'RoleController@update')
     ->where(['user' => '\d+', 'role' => '\d+'])
     ->middleware('can:update,role');

Route::delete('/users/{user}/roles/{role}', 'RoleController@destroy')
     ->where(['user' => '\d+', 'role' => '\d+'])
     ->middleware('can:delete,role');

Route::post('/users/{user}/roles', 'RoleController@store')
     ->where('user', '\d+')
     ->middleware('can:create,App\Role');

/**
 * Old routes
 *
 * @todo: clean up
 */
Route::get('/', function () {
    return view('welcome');
});
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/images/raw', 'ImageController@listRaw');
Route::get('/images/{image}', 'ImageController@get')->where('image', '\d+');
Route::get('/images/raw/search/{query}', 'ImageController@searchRaw')->where('query', '.*');
Route::post('/images/raw', 'ImageController@storeRaw');
Route::delete('/images/{id}', 'ImageController@deleteRaw')->where('id', '\d+');
