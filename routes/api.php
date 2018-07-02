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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| USERS (end_users)
|--------------------------------------------------------------------------
|
*/

Route::middleware('auth:api')->post('alma', 'EndUserController@store');

//List users collection
Route::get('end_users', 'EndUserController@index');

//List a single user
Route::middleware('auth:api')->get('end_user/{id}', 'EndUserController@show');

//Add new user
Route::middleware('auth:api')->post('end_user', 'EndUserController@store');

//Update user
Route::middleware('auth:api')->put('end_user', 'EndUserController@store');

//Delete user
Route::middleware('auth:api')->delete('end_user/{id}', 'EndUserController@destroy');

//import from CSV
Route::middleware('auth:api')->get('end_users/import', 'EndUserController@importFromCSV');
Route::middleware('auth:api')->get('end_users/import/points', 'EndUserController@importPointsFromCSV');

//Mass check users
Route::get('end_users/mass_check/{$chanel}', 'EndUserController@massCheck');
Route::middleware('auth:api')->get('end_users/mass_process/', 'EndUserController@massProcess');
Route::get('end_users/check_online/{chanel}', 'EndUserController@checkOnlineUsers');

/*
|--------------------------------------------------------------------------
| CAMPAIGNS
|--------------------------------------------------------------------------
|
*/
//List campaign collection
Route::middleware('auth:api')->get('campaigns', 'CampaignController@index');

//List a single campaign
Route::middleware('auth:api')->get('campaign/{id}', 'CampaignController@show');

//Add new campaign
Route::middleware('auth:api')->post('campaign', 'CampaignController@store');

//Update campaign
Route::middleware('auth:api')->put('campaign', 'CampaignController@store');

//Delete campaign
Route::middleware('auth:api')->delete('campaign/{id}', 'CampaignController@destroy');

/*
|--------------------------------------------------------------------------
| POINTS
|--------------------------------------------------------------------------
|
*/
//List points feed
Route::middleware('auth:api')->get('points', 'PointsController@index');

//List single users points
Route::middleware('auth:api')->get('point/{user_id}', 'PointsController@show');

//Give back sum of points to user
Route::get('points/sum', 'PointsController@sum');

//Add new point
Route::middleware('auth:api')->post('point', 'PointsController@store');

//Add new point to all user
Route::middleware('auth:api')->post('point/to_all', 'PointsController@addPointsToAll');

//Add new point to all user in the array by id's
Route::middleware('auth:api')->post('points/for_every_online', 'PointsController@addPointsToOnline');

//Update point
Route::middleware('auth:api')->put('point', 'PointsController@store');

//Delete point
Route::middleware('auth:api')->delete('point/{id}', 'PointsController@destroy');

/*
|--------------------------------------------------------------------------
| DROPS
|--------------------------------------------------------------------------
|
*/
//List all drops
Route::middleware('auth:api')->get('drops', 'DropsController@index');

//Activate ALL drops
Route::middleware('auth:api')->get('drops/activate', 'DropsController@activateAll');

//Deactivate ALLL drops
Route::middleware('auth:api')->get('drops/deactivate', 'DropsController@deactivateAll');

//Get active drops
Route::middleware('auth:api')->get('drops/active', 'DropsController@activeDrops');

//Activate specific drop
Route::middleware('auth:api')->get('drop/activate/{id}', 'DropsController@activateDrop');

//Deactivate specific drop
Route::middleware('auth:api')->get('drop/deactivate/{id}', 'DropsController@deactivateDrop');

//Buy drop
Route::post('drop/buy/', 'DropsController@buyDrop');

//Create new drop
Route::middleware('auth:api')->post('drop', 'DropsController@store');

//Modify drop
Route::middleware('auth:api')->put('drop', 'DropsController@store');

//Delete drop
Route::middleware('auth:api')->delete('drop/{id}', 'DropsController@destroy');


/*
|--------------------------------------------------------------------------
| QUESTIONS
|--------------------------------------------------------------------------
|
*/

//List all questions
Route::get('questions', 'QuestionController@index');

//add new question
Route::middleware('auth:api')->post('question', 'QuestionController@store');

//modify question
Route::middleware('auth:api')->put('question', 'QuestionController@store');

//delete question
Route::middleware('auth:api')->delete('question/{id}', 'QuestionController@destroy');

//get question gate for drop
Route::get('question_gate/get/{drop_id}', 'QuestionController@getQuestionGate');

//set Question gate for Drop
Route::middleware('auth:api')->post('question_gate/set', 'QuestionController@setQuestionGate');

/*
|--------------------------------------------------------------------------
| SETTINGS
|--------------------------------------------------------------------------
|
*/
Route::middleware('auth:api')->post('settings', 'SettingsController@store');
Route::middleware('auth:api')->get('settings', 'SettingsController@index');


/*
|--------------------------------------------------------------------------
| AUTO COMPLETE
|--------------------------------------------------------------------------
|
*/
Route::get('user_search', 'AutoCompleteController@ajaxData');