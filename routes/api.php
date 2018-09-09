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

Route::post('createSubcriber', 'SubscriberController@createSubcriber')->name('createSubcriber');
Route::get('getSubscribers', 'SubscriberController@retrieveSubscribers')->name('retrieveSubscribers');
Route::get('getSubscriber/{id}', 'SubscriberController@retrieveSubscriber')->name('retrieveSubscriber');
Route::delete('deleteSubscriber/{id}', 'SubscriberController@deleteSubscriber')->name('deleteSubscriber');
Route::post('updateSubscriber/{id}', 'SubscriberController@updateSubscriber')->name('updateSubscriber');
Route::post('updateSubscriberFields/{id}', 'SubscriberController@updateSubscriberFields')->name('updateSubscriberFields');
Route::post('addSubscriberFields/{id}', 'SubscriberController@addSubscriberFields')->name('addSubscriberFields');
Route::delete('deleteSubscriberFields/{id}', 'SubscriberController@deleteSubscriberFields')->name('deleteSubscriberFields');
