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



Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Auth::routes();

Route::get('/dashboard', 'PagesController@getDashboard')->middleware('auth');

Route::get('/points', 'PagesController@getPoints')->middleware('auth');

Route::get('/end_users', 'PagesController@getEndUsers')->middleware('auth');

Route::get('/campaigns', 'PagesController@getCampaigns')->middleware('auth');

Route::get('/chatbot', 'PagesController@getChatBot')->middleware('auth');

Route::get('/drops', 'PagesController@getDrops')->middleware('auth');

Route::get('/questions', 'PagesController@getQuestions')->middleware('auth');

//Caspar specific routes
Route::get('/caspar/winner/{name}', 'PagesController@getCasparWinner');
Route::get('/caspar/chatbot/status', 'PagesController@getCasparChatbotStatus');
