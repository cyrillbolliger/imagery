<?php

use Spatie\WelcomeNotification\WelcomesNewUsers;

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

Route::prefix('api/1')->middleware('auth')->group(function () {
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

    Route::post('/users/logout', 'UserController@logout')
         ->middleware('can:logout,App\User');

    Route::post('/users/{user}/invite', 'UserController@invite')
         ->where('user', '\d+')
         ->middleware('can:manage,user');

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

    Route::get('/groups/{group}/logos', 'LogoController@listByGroup')
         ->where('group', '\d+')
         ->middleware('can:view,group');

    Route::get('/groups/{group}/users', 'UserController@listByGroup')
         ->where('group', '\d+')
         ->middleware('can:view,group');

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

    Route::post('/logos', 'LogoController@store')
         ->middleware('can:create,App\Logo');

    /**
     * Logo Files
     */
    Route::get('/files/logos/{logo}/{color}', 'FileController@show')
         ->where('logo', '\d+')
         ->where('color', '(white)|(green)')
         ->middleware('can:view,logo')
         ->name('logo');

    /**
     * Images
     */
    Route::get('/images/{image}', 'ImageController@show')
         ->where('image', '\d+')
         ->middleware('can:view,image');

    Route::get('/images/raw', 'ImageController@indexRaw')
         ->middleware('can:viewAny,App\Image');

    Route::get('/images/final', 'ImageController@indexFinal')
         ->middleware('can:viewAny,App\Image');

    Route::get('/images/final/search/{terms}', 'ImageController@searchFinal')
         ->middleware('can:viewAny,App\Image');

    Route::put('/images/{image}', 'ImageController@update')
         ->where('image', '\d+')
         ->middleware('can:update,image');

    Route::delete('/images/{image}', 'ImageController@destroy')
         ->where('image', '\d+')
         ->middleware('can:delete,image');

    Route::post('/images', 'ImageController@store')
         ->middleware('can:create,App\Image');

    /**
     * Image Files
     */
    Route::get('/files/images/{image}', 'FileController@show')
         ->where('image', '\d+')
         ->middleware('can:view,image')
         ->name('image');

    Route::get('/files/images/{image}/thumbnail', 'FileController@showThumbnail')
         ->where('image', '\d+')
         ->middleware('can:view,image')
         ->name('thumbnail');

    Route::post('/files/images', 'FileController@storeChunk')
         ->middleware('can:create,App\Image');

    /**
     * Legal
     */
    Route::get('/images/{image}/legal', 'LegalController@show')
         ->where('image', '\d+')
         ->middleware('can:view,image');

    Route::put('/images/{image}/legal', 'LegalController@update')
         ->where('image', '\d+')
         ->middleware('can:update,image');

    // no delete route: delete the image (then the legal is deleted as well)

    Route::post('/images/{image}/legal', 'LegalController@store')
         ->where('image', '\d+')
         ->middleware('can:update,image'); // the legal is an extension of the image

    /**
     * Fonts
     *
     * Load them using the protected routes because we do only have a licence
     * to use but not to distribute the fonts.
     */
    Route::get('/files/fonts/{font}', 'FontController@show')
         ->where('font', '[\w-\.]+')
         ->name('fonts');
});

/**
 * The Frontend
 */
Route::middleware(WelcomesNewUsers::class)->group(function () {
    Route::get('welcome/{user}', 'Auth\WelcomeController@showWelcomeForm')
         ->where('user', '\d+')
         ->name('welcome');

    Route::post('welcome/{user}', 'Auth\WelcomeController@savePassword')
         ->where('user', '\d+');
});

Auth::routes();

Route::get('/', 'HomeController@index');

/**
 * "Catch all" route
 *
 * This route is necessary for the vuejs router to work
 */
Route::fallback('HomeController@index');
