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


Route::post('login','API\PassportController@login');
Route::post('register','API\PassportController@register');

Route::group(['middleware' => 'auth:api'],function(){
	Route::post('get-details' , 'API\PassportController@getDetails');
	Route::post('event_create','EventsController@create_event');
	Route::post('event_description','EventsController@create_event_description');
	Route::get('event_create/{event_id}','EventsController@show_event_detail');
	Route::get('event_description/{event_id}','EventsController@show_event_desciption');
	Route::post('create_address','EventsController@create_address');
	Route::get('show_address/{event_id}','EventsController@show_address');
	Route::post('create_ticket','EventsController@create_ticket');
	Route::get('show_ticket/{event_id}','EventsController@show_tickets');
	Route::get('publish/event','EventsController@show_unpublished_event');
	Route::post('book_ticket','EventsController@store_ticket_booking');
	Route::get('publish_the_event/{event_id}','EventsController@publish_the_event');
	Route::get('delete/{event_id}','EventsController@delete_the_event');
});





