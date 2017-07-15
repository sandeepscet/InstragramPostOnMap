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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/home/getInstaPost', 'HomeController@getInstaPost')->name('getInstaPost');
Route::post('/home/saveInstaPost', 'HomeController@saveInstaPost')->name('saveInstaPost');
Route::get('/home/saveInstaPost', 'HomeController@saveInstaPost')->name('saveInstaPost');
Route::get('/home/listInstaPost', 'HomeController@listInstaPost')->name('listInstaPost');
Route::get('/home/list', 'HomeController@list')->name('list');
