<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    echo "<center> Welcome </center>";
});

$router->get('/version', function () use ($router) {
    return $router->app->version();
});

Route::group([

    'prefix' => 'api'

], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::put('update/{id}', 'AuthController@update');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('session', 'AuthController@me');
    Route::get('single/{id}', 'AuthController@singleUser');
    Route::get('all', 'AuthController@allUsers');

    Route::post('company/store', 'CompanyController@store');
    Route::put('company/update/{id}', 'CompanyController@update');
    Route::get('company/single/{id}', 'CompanyController@single');
    Route::delete('company/delete/{id}', 'CompanyController@delete');
    Route::get('company/all', 'CompanyController@all');
    Route::get('company/tree', 'CompanyController@tree');

});
