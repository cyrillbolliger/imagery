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
 * Groups
 */
Route::get('/groups/{group}', 'GroupController@show')
     ->where('group', '\d+')
     ->middleware('can:view,group');

Route::get('/groups', 'GroupController@index')
     ->middleware('can:viewAny,App\Group');

Route::put('/groups/{group}', 'GroupController@update')
     ->where('group', '\d+')
     ->middleware('can:update,group');

Route::delete('/groups/{group}', 'GroupController@destroy')
     ->where('group', '\d+')
     ->middleware('can:delete,group');

Route::post('/groups', 'GroupController@store')
     ->middleware('can:create,App\Group');

/**
 * Logos
 */
Route::get('/logos/{logo}', 'LogoController@show')
     ->where('logo', '\d+')
     ->middleware('can:view,logo');

Route::get('/logos', 'LogoController@index')
     ->middleware('can:viewAny,App\Logo');

Route::put('/logos/{logo}', 'LogoController@update')
     ->where('logo', '\d+')
     ->middleware('can:update,logo');

Route::delete('/logos/{logo}', 'LogoController@destroy')
     ->where('logo', '\d+')
     ->middleware('can:delete,logo');

//Route::post('/logos', 'LogoController@store')
//     ->middleware('can:create,App\Logo');

/**
 * Logo Files
 */
Route::get('/files/logos/{logo}', 'FileController@show')
     ->where('logo', '\d+')
     ->middleware('can:view,logo')
     ->name('logo');

Route::post('/files/logos', 'FileController@storeChunk')
     ->middleware('can:create,App\Logo');

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
