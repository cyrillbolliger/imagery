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

Route::get('/raw-images', 'ImageController@listRaw');
Route::get('/raw-image/{id}', 'ImageController@getRaw')->where('id', '\d+');
Route::get('/raw-image/search/{query}', 'ImageController@searchRaw')->where('query', '.*');
Route::post('/raw-image', 'ImageController@storeRaw');
Route::delete('/raw-image/{id}', 'ImageController@deleteRaw')->where('id', '\d+');


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
