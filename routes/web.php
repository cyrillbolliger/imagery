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


use App\User;

Route::get('/images/raw', 'ImageController@listRaw');
Route::get('/images/{image}', 'ImageController@get')->where('image', '\d+');
Route::get('/images/raw/search/{query}', 'ImageController@searchRaw')->where('query', '.*');
Route::post('/images/raw', 'ImageController@storeRaw');
Route::delete('/images/{id}', 'ImageController@deleteRaw')->where('id', '\d+');

Route::get('/users/{user}', function (User $user) {
    return $user;
})->where('user', '\d+')->middleware('can:get,user');



Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
