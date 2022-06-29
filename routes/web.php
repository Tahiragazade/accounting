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
$router->get('/', function () use ($router) {
    return view('users.index');
});

$router->get('/version', function () use ($router) {
    return $router->app->version();
});

Route::group([

    'prefix' => 'api'

], function ($router) {
    Route::post('/login', 'AuthController@login');
    Route::post('users/register', 'AuthController@register');
    Route::put('users/update/{id}', 'AuthController@update');
    Route::post('users/logout', 'AuthController@logout');
    Route::post('users/refresh', 'AuthController@refresh');
    Route::get('users/session', 'AuthController@me');
    Route::get('users/single/{id}', 'AuthController@singleUser');
    Route::get('users/all', 'AuthController@allUsers');

    Route::post('company/store', 'CompanyController@store');
    Route::put('company/update/{id}', 'CompanyController@update');
    Route::get('company/single/{id}', 'CompanyController@single');
    Route::delete('company/delete/{id}', 'CompanyController@delete');
    Route::get('company/all', 'CompanyController@all');
    Route::get('company/tree', 'CompanyController@tree');

    Route::post('transaction/store', 'TransactionController@store');
    Route::put('transaction/update/{id}', 'TransactionController@update');
    Route::get('transaction/single/{id}', 'TransactionController@single');
    Route::delete('transaction/delete/{id}', 'TransactionController@delete');
    Route::get('transaction/all', 'TransactionController@all');
    Route::get('transaction/tree', 'TransactionController@tree');
    Route::get('transaction/payment', 'TransactionController@payment');

});
