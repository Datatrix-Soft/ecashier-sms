<?php

/** @var \Laravel\Lumen\Routing\Router $router */
use Illuminate\Support\Facades\Route;
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
    return $router->app->version();
});

Route::group(['prefix' => 'sms'], function () {
    Route::get('/list', 'SmsController@index');
    Route::post('/create', 'SmsController@store');
    Route::post('bulk/create', 'SmsController@bulkStore');
    Route::get('/balance', 'SmsController@getBalance');
    Route::post('/update', 'SmsController@update');
});
