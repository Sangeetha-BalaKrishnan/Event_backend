<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\event;
use App\event_view;
use Datetime , DB;
use App\User , App\ticket;
use Illuminate\Support\Facades\Auth;
use Excel;

class ReportsController extends Controller
{
    public function download_sales_report($eventid)
    {
    	$bookedEvents = DB::table($eventid.'_tickets')->where('status',"true")->get();
	        $data = array();
	        foreach ($bookedEvents as $event) {
	            $tempdata = array();
	            $user = User::where('userid',$event->userid)->first();
	            if($user != null)
	            {
	            	$tempdata['username'] = $user->name;	
	            	$tempdata['phone'] = $user->phone;
	            	$tempdata['email'] = $user->email;
	            }
	            else
	            {
	            	$tempdata['username'] = 'NUll';
	            	$tempdata['phone'] = 'Null';
	            	$tempdata['email'] = 'Null';
	            }
	            // dd($event);
	            $tempdata['ticket_name'] = $event->ticket_name;
	            $tempdata['total_ticket'] = $event->total_ticket;
	            $tempdata['cost'] = $event->cost;
	            $tempdata['booked_at'] = $event->updated_at;
	            array_push($data,$tempdata);
	        }
    	$eventName = event::where('event_id',$eventid)->first();
	        $filename = $eventName->event_name.'_'.time();
		return Excel::create($filename, function($excel) use ($data) {
			$excel->sheet('mySheet', function($sheet) use ($data)
	        {
				$sheet->fromArray($data);
	        });
		})->download('csv');

    	// return Excel::download(new User, 'users.xlsx');
    }
}
