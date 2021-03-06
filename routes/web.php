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

Route::any('/wechat', 'WechatController@serve');
Route::any('/test', 'WechatController@test');
Route::get('/test/user', 'WechatController@testUser');
Route::get('/oauth', 'WechatController@authUser')->middleware(['web','wechat.oauth']);
Route::get('/menu/create', 'WechatController@createMenu');
