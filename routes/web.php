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
Route::get('show/upcoming_events','DisplayController@display_upcoming_events');
Route::get('show/events','DisplayController@display_events');
Route::get('show/trending_events','DisplayController@display_trending_events');

Route::get('user/changePassword','UserController@check_password');


Route::get('event/{event_url}','EventsController@display_event_details');
Route::get('check_url_exists/{url}','EventsController@check_event_url_exists');