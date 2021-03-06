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

Route::get('/encrypt', 'pagesController@encrypt')->name('test');
Route::get('/decrypt', 'pagesController@decrypt')->name('decrypt');

Route::get('/', 'pagesController@index')->name('index');

Route::get('/runRuby', 'pagesController@runRuby')->name('runRuby');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
