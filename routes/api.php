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
	//To show unpublish event
	Route::get('publish/event','EventsController@show_unpublished_event');
	Route::post('book_ticket','Bookevents@book_ticket_booking');
	Route::post('save_ticket','Bookevents@store_ticket_booking');
	Route::get('publish_the_event/{event_id}','EventsController@publish_the_event');
	Route::get('unpublish_the_event/{event_id}','EventsController@publish_the_event');
	
	Route::get('delete/{event_id}','EventsController@delete_the_event');
	Route::get('manage_event/{eventid}','EventsController@manage_events');
	Route::get('profile','UserController@show_profile');
	Route::get('editprofile/{id}','UserController@edit_profile');
	Route::post('create_image','EventsController@image_upload');
	Route::get('show_image/{eventid}','EventsController@show_image');
});
Route::get('report_download/{eventid}','ReportsController@download_sales_report');







