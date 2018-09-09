<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('getFields', 'FieldController@retrieveFields')->name('retrieveFields');
Route::get('getField/{id}', 'FieldController@retrieveField')->name('retrieveField');
Route::post('createField', 'FieldController@createField')->name('createField');
Route::delete('deleteField/{id}', 'FieldController@deleteField')->name('deleteField');
