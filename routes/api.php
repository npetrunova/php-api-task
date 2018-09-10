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

Route::get('getFields', 'FieldController@retrieveFields');
Route::get('getField/{id}', 'FieldController@retrieveField');
Route::post('createField', 'FieldController@createField');
Route::delete('deleteField/{id}', 'FieldController@deleteField');

Route::post('createSubcriber', 'SubscriberController@createSubcriber');
Route::get('getSubscribers', 'SubscriberController@retrieveSubscribers');
Route::get('getSubscribersByState/{state}', 'SubscriberController@retrieveSubscribersByState');
Route::get('getSubscriber/{id}', 'SubscriberController@retrieveSubscriber');
Route::delete('deleteSubscriber/{id}', 'SubscriberController@deleteSubscriber');
Route::post('updateSubscriber/{id}', 'SubscriberController@updateSubscriber');
Route::post('updateSubscriberState/{id}', 'SubscriberController@updateSubscriberState');
Route::post('updateSubscriberFields/{id}', 'SubscriberFieldController@updateSubscriberFields');
Route::post('addSubscriberFields/{id}', 'SubscriberFieldController@addSubscriberFields');
Route::delete('deleteSubscriberFields/{id}', 'SubscriberFieldController@deleteSubscriberFields');
