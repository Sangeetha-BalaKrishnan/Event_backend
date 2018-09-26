<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DateTime;
use DateInterval;
use App\event;

class DisplayController extends Controller
{
    //To display upcoming events
    public function display_upcoming_events()
    {	
        try {
        date_default_timezone_set('Asia/Kolkata');
        //To Get Today's data
        $now = date('Y-m-d');
        
        //To Get the upcoming Date
        $upcomingDate = date('Y-m-d',strtotime("+1 month" ,strtotime($now)));

        $data = array();
        $upcomingEvents = event::where([['start_timestamp','>',$upcomingDate],['publish',1]])->get();
        foreach ($upcomingEvents as $upcomingEvent) {
            $data1 = array();
            $data1['event_id'] = $upcomingEvent->event_id;
            $data1['event_name'] = $upcomingEvent->event_name;
            $data1['timestamp'] = $upcomingEvent->start_timestamp;
            $data1['image'] = $upcomingEvent->images;
            $data1['url_name'] = "https://thetickets.in/event/".$upcomingEvent->url_name;
            array_push($data,$data1);
        }
        if(count($data))
            return response()->json(['status'=>true,'data'=>$data]);
        else
            return response()->json(['status'=>false,'error'=>'There are no upcoming events. Please try again after some time.']);

    } catch (\Exception $e) {
        return response()->json(['status'=>false,'error'=>'Please Try again after some time']);
    }
    	
    }
    // To display Events on the page
    public function display_events()
    {

        try {
           
           date_default_timezone_set('Asia/Kolkata');
            //To Get Today's data
            $now = date('Y-m-d');
            //To Get the upcoming Date
            $upcomingDate = date('Y-m-d',strtotime("+1 month" ,strtotime($now)));

            $data = array();
            $image = array();
            $Events = event::where([['start_timestamp','<',$upcomingDate],['publish',1]])->get();
            foreach ($Events as $Event) {
                $data1 = array();
                $image1 = array();
                $data1['event_id'] = $Event->event_id;
                $data1['event_name'] = $Event->event_name;
                $data1['timestamp'] = date('d-m-Y H:i a',strtotime($Event->start_timestamp));
                $data1['image'] = $Event->images;
                $data1['url_name'] = "https://thetickets.in/event/".$Event->url_name;
                $image1['image'] = $Event->images;
                $image1['url_name'] = "https://thetickets.in/event/".$Event->url_name;
                array_push($image,$image1);
                array_push($data,$data1);
            }
            if(count($data))
                return response()->json(['status'=>true,'data'=>$data ,'image'=>$image]); 
            else
                return respoonse()->json(['status'=>false,'error'=>'Currenly There are no Events To display']);
        } catch (\Exception $e) {
            return response()->json(['status'=>false,'error'=>'Please Try again after some time.']);
        }

    }
    // To display Trensing Events in the frontend
    public function display_trending_events()
    {

        try {

            $data = array();
            $no = 10;
            //To search for events with max views
            $trendingEvents = event::where('views','>',$no)->get();
            foreach ($trendingEvents as $trendingEvent) {
                $data1 = array();
                $data1['event_id'] = $trendingEvent->event_id;
                $data1['event_name'] = $trendingEvent->event_name;
                $data1['timestamp'] = $trendingEvent->start_timestamp;
                $data1['image'] = $trendingEvent->images;
                $data1['url_name'] = "https://thetickets.in/event/".$trendingEvent->url_name;
                array_push($data,$data1);
            if(count($data))
                return response()->json(['status'=>true,'data'=>$data]); 
            else
                return respoonse()->json(['status'=>false,'error'=>'Currenly There are no trending Events To display']);
        } 
    }
        catch (\Exception $e) {
            return respoonse()->json(['status'=>false,'error'=>'Please Try again after soem time.']);
        }
    	
    }


   

}
