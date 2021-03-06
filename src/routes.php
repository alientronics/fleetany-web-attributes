<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(
    [
    'middleware' => ['auth',
    'acl'],
    'can' => 'view.attribute'],
    function () {
        Route::resource('attribute', '\Alientronics\FleetanyWebAttributes\Controllers\AttributeController');
    }
);

Route::get('attribute/destroy/{id}', '\Alientronics\FleetanyWebAttributes\Controllers\AttributeController@destroy');
Route::get('attribute/download/{file}', '\Alientronics\FleetanyWebAttributes\Controllers\AttributeController@download');