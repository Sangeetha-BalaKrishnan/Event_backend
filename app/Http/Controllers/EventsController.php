<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\event;
use App\event_view;
use Datetime , DB;
use App\User , App\ticket;
use Illuminate\Support\Facades\Auth;

class EventsController extends Controller
{
     public function display_event_details($event_url)
    {
        try {
        	$events = event::where([['url_name',strtolower($event_url)],['publish',1]])->first();
            if($events != null)
            {
            	$user = User::where('userid',$events->userid)->first();
            	$data=array();
                $event_details = array();
                $address = array();
                $event_details['name'] = $events->event_name;
                $event_details['description'] = $events->event_description;
                $event_details['start_time'] = date('d-m-Y H:i a',strtotime($events->start_timestamp));
                $event_details['end_time'] = date('d-m-Y H:i a',strtotime($events->end_timestamp));;
                $event_details['organizer']= $user->name;
                $event_details['phone'] = $user->phone;

                $data['eventid'] =$events->event_id;
                $data['event_details'] = $event_details;
                $data['image'] = $events->images;
                
                $address['address'] = $events->address;
                $address['lat_long'] = $events->lat_long;

                $data['location'] = $address;

                $tickets = ticket::where('event_id',$events->event_id)->get();
                $data1 = array();
                $ticketname = array();
                $tickettotal = array();
                $ticketcost = array();
                $dummyTicket = array();
                $totalTickets =0;
                foreach ($tickets as $key=>$ticket) {
                	$totalTickets = 10-$ticket->ticket_total;
                	$totalTickets=($totalTickets<0)?$ticket->ticket_total:$totalTickets;
                    array_push($ticketname, $ticket->ticket_name);
                    array_push($tickettotal, $totalTickets);
                    array_push($ticketcost, intval($ticket->ticket_cost));
                    array_push($dummyTicket , 0);
                }
                array_push($data1,[$ticketname,$tickettotal,$ticketcost,$dummyTicket]);
                $data['tickets'] = $data1[0];
                return response()->json(['status'=>true,'data'=>$data]);
            }
            else
                return response()->json(['status'=>false,'error'=>'No Event Exists on this url']);

        } catch (\Exception $e) {
        	// dd($e);
            return response()->json(['status'=>false,'error'=>'No Event Exists !!']);            
        }

    }


    public function check_event_url_exists($url)
    {
    	try {
    		
    		$url = event::where('url_name',$url)->first();
    		if($url == null)
    			return response()->json(['status'=>true]);
    		else
    			return response()->json(['status'=>false,'error'=>'The Event url already exists , try another name.']);

    	} catch (Exception $e) {
    		
    		return response()->json(['status'=>false,'error'=>'The url already exists']);
    	}
    }

    public function create_event(Request $r)
    {
    	try {
    		$name = $r->event_name;
            $url = strtolower($r->event_url);
            $organizer = $r->event_organizer;
            $start_time = $r->start;
            $end_time = $r->end;
            $event_id = $r->event_id;
            $user = Auth::User();
            if($r->event_id == "null" || $r->event_id == null)
            {
                $event_id = self::generate_event_id($name);
                $event = new event;
                $event->event_id = $event_id;
                $event->userid = $user->userid;
                $event->event_name = $name;
                $event->url_name = $url;
                $event->start_timestamp=$start_time;
                $event->end_timestamp= $end_time;
                $event->category = $r->category;
                $event->organizer = $organizer;
                $event->save();

                $tableName = $event_id.'_tickets';
                $table = "CREATE TABLE `".$tableName."` (
                          `id` int(11)  NOT NULL AUTO_INCREMENT PRIMARY KEY,
                          `eventid` varchar(250) NOT NULL,
                          `uniqueid` int(30) NOT NULL,
                          `userid` varchar(250) NOT NULL,
                          `ticket_name` varchar(300) NOT NULL,
                          `total_ticket` int(15) NOT NULL,
                          `cost` decimal(15,2) NOT NULL,
                          `status` varchar(15) NOT NULL,
                          `created_at` datetime NOT NULL,
                          `updated_at` datetime NOT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
                        DB::statement($table);
            }
            else
            {
                $event = event::where('event_id',$event_id)->update(['userid'=>$user->userid,'event_name'=>$name,'url_name'=>$url,'start_timestamp'=>$start_time,'end_timestamp'=>$end_time,'organizer'=>$organizer ,'category'=>$r->category]);
            }
    		
			return response()->json(['status'=>true,'eventId'=>$event_id]);


    	} catch (\Exception $e) {
    		DD($e);
    		return response()->json(['status'=>false,'error'=>'Please Try again after some time']);
    	}
    }

    public function show_event_detail($event_id)
    {
    	try {
    		
    		$data = array();
    		$event = event::where('event_id',$event_id)->first();
            try {
                $data['event_name'] = $event->event_name;
                $data['url'] = $event->url_name;
                $data['start'] = $event->start_timestamp;
                $data['end'] = $event->end_timestamp;
                $data['organizer'] = $event->organizer;
                $data['event_id'] = $event->event_id;
                $data['category'] = $event->category;		

                return response()->json(['status'=>true,'data'=>$data]);    
            } catch (\Exception $e) {
              return response()->json(['status'=>false,'error'=>'No EVent found with the id']);
            }
    		


    	} catch (\Exception $e) {
    		// DD($e);
    		return response()->json(['status'=>false,'error'=>'Please Try again after some time']);
    	}
    }

    public function create_event_description(Request $r)
    {
    	try {
    		
    		$description = $r->description;
	    	$eventid = $r->event_id;
	    	// dd($eventid);
	    	$event = event::where('event_id',$eventid)->update(['event_description'=>$description]);
	    	return response()->json(['status'=>true,'eventId'=>$eventid]);	
    	} catch (\Exception $e) {
    		return response()->json(['status'=>false,'error'=>'Please Try again after some time']);	
    	}

    }

    public function show_event_desciption($event_id)
    {
    	try {
    		
    		$data = array();
    		$event = event::where('event_id',$event_id)->first();
    		$data['description'] = $event->event_description;
    		$data['event_id'] = $event->event_id;

			return response()->json(['status'=>true,'data'=>$data]);


    	} catch (\Exception $e) {
    		// DD($e);
    		return response()->json(['status'=>false,'error'=>'Please Try again after some time']);
    	}
    }
    public function create_address(Request $r)
    {
        try {
            $event = event::where('event_id',$r->eventid)->update(['address'=>$r->address,'lat_long'=>$r->lat_long]);
            return response()->json(['status'=>true,'eventId'=>$r->eventid]);  
            
        } catch (\Exception $e) {
            return response()->json(['status'=>false]);       
        }
    }
    public function show_address($eventid)
    {
        try {
            $event = event::where('event_id',$eventid)->first();
            $data['address'] = $event->address;
            $data['lat_long'] = $event->lat_long;
            return response()->json(['status'=>true , 'data' =>$data]);
        } catch (\Exception $e) {
            return response()->json(['status'=>false]);   
        }
    }
    public function create_ticket(Request $r)
    {
        try {   
            $ticket_name = $r->ticket_name;
            $ticket_max = $r->ticket_max;
            $ticket_price = $r->ticket_price;
            $now = date('Y-m-d H:i:s');
            $id=$r->id;
            if(!count($id))
            {
            	for ($i=0; $i < $r->total ; $i++) {
	            	$ticket = new ticket;
	                $ticket->event_id = $r->eventid;
	                $ticket->ticket_name = $ticket_name[$i];
	                $ticket->ticket_total = $ticket_max[$i];
	                $ticket->ticket_cost = $ticket_price[$i];
	                $created_at = $now;
	                $updated_at = $now;
	                $ticket->save();
	            }	
            }
            else
            {
            	for ($i=0; $i < $r->total ; $i++) {
            		try {
            			$ticket = ticket::find($id[$i]);
		                $ticket->event_id = $r->eventid;
		                $ticket->ticket_name = $ticket_name[$i];
		                $ticket->ticket_total = $ticket_max[$i];
		                $ticket->ticket_cost = $ticket_price[$i];
		                $created_at = $now;
		                $updated_at = $now;
		                $ticket->save();	
            		} catch (\Exception $e) {
            			$ticket = new ticket;
		                $ticket->event_id = $r->eventid;
		                $ticket->ticket_name = $ticket_name[$i];
		                $ticket->ticket_total = $ticket_max[$i];
		                $ticket->ticket_cost = $ticket_price[$i];
		                $created_at = $now;
		                $updated_at = $now;
		                $ticket->save();
            		}
	            	
	            }
        		
            }
            
            return response()->json(['status'=>true,'eventId'=>$r->eventid]);
        } catch (\Exception $e) {
            return response()->json(['status'=>false]);  
        }
    }
    public function show_tickets($eventid)
    {
        try {
            $tickets = ticket::where('event_id',$eventid)->get();
            $data = array();
            $ticketname = array();
            $tickettotal = array();
            $ticketcost = array();
            $id = array();
            foreach ($tickets as $key=>$ticket) {
                array_push($ticketname, $ticket->ticket_name);
                array_push($tickettotal, $ticket->ticket_total);
                array_push($ticketcost, intval($ticket->ticket_cost));
                array_push($id,$ticket->id);
            }
            array_push($data,[$ticketname,$tickettotal,$ticketcost]);
            $data = $data[0];
            return response()->json(['status'=>true , 'data' =>$data ,'id'=>$id,'eventid'=>$eventid]);
        } catch (\Exception $e) {
            return response()->json(['status'=>false]);   
        }

    }
    public function show_unpublished_event()
    {
        try
        {
            $user = Auth::user();
            $now = date('Y-m-d H:i:s');
            $publish = array();
            $published_events = event::where([['start_timestamp','>',$now],['publish',null]])->get();
            foreach ($published_events as $pub) {
                $value['event_name']= $pub->event_name;
                $value['start'] = $pub->start_timestamp;
                $value['end'] = $pub->end_timestamp;
                $value['url'] = $pub->url_name;
                $value['event_id']=$pub->event_id;
                $value['publish'] = false;
                array_push($publish, $value);
            }
            if(count($publish))
                return response()->json(['status'=>true,'data'=>$publish]);
            else
                return response()->json(['status'=>false,'error'=>'No Events To Publish']);
                
        }
        catch(\Exception $e)
        {
            return response()->json(['status'=>false,'error'=>'No Events To Display.']);
        }
        

    }



     private static function generate_event_id($userName)
    {
        //Create a Random Number 
        $min = 10;
        $max = 1000000;
        $loop=1;
        while($loop)
        {
            //Generate Randome Number
            $randomValue = rand($min,$max);

            //Create a Database Name using the First Word of Oragnization name and inserting random value in middle
            $eventId = strtolower(substr($userName,0,1).$randomValue.substr($userName,-1));

            //Check whether the Database name exists in the Organization table
            $checkeventId =event::where('event_id',$eventId)->get();

            $loop = (count($checkeventId) == 0)?0:1;

        }
        return $eventId;         
    }

    private static function generate_unique_id($tableName)
    {
        //Create a Random Number 
        $min = 10;
        $max = 1000000;
        $loop=1;
        while($loop)
        {
            //Generate Randome Number
            $randomValue = rand($min,$max);

            //Create a Database Name using the First Word of Oragnization name and inserting random value in middle
            $uniqueid = $randomValue;

            //Check whether the Database name exists in the Organization table
            $checkuniqueid =DB::table($tableName)->where('uniqueid',$uniqueid)->get();

            $loop = (count($checkuniqueid) == 0)?0:1;

        }
        return $uniqueid;         
    }

    
    public function publish_the_event($eventid)
    {
        try {
            event::where('event_id',$eventid)->update(['publish'=>1]);
            return response()->json(['status'=>true]);
        } catch (\Exception $e) {
            // dd($e);
            return response()->json(['status'=>false]);
        }
    }

    public function unpublish_the_event($eventid)
    {
        try {
            event::where('event_id',$eventid)->update(['publish'=>null]);
            return response()->json(['status'=>true]);
        } catch (\Exception $e) {
            // dd($e);
            return response()->json(['status'=>false]);
        }
    }
    public function delete_the_event($eventid)
    {
        try {
            event::where('event_id',$eventid)->delete();
            return response()->json(['status'=>true]);
        } catch (\Exception $e) {
            return response()->json(['status'=>false]);
        }
    }


    public function show_published_event()
    {
        $events = event::where([['userid',$userid],['publish',1]])->get();
        foreach ($events as $key => $value) {

            
        }


    }

    public function manage_events($eventid)
    {
        // $userid = Auth::user()->userid;
        // $events = event::where('userid',$userid)->get();
        try {
        	$bookedEvents = DB::table($eventid.'_tickets')->where('status',"true")->get();
	        if(!count($bookedEvents))
	        	return response()->json(['status'=>false,'error'=>'No sales report Yet']);
	        $data = array();
	        foreach ($bookedEvents as $event) {
	            $tempdata = array();
	            $user = Auth::User();
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
	        return response()->json(['status'=>true,'data'=>$data]);	
        } catch (\Exception $e) {
        	dd($e);
        	return response()->json(['status'=>false,'error'=>'No event pressent']);
        }
        
    }

    public function book_an_event(Request $r)
    {
    	if(Auth::check())
    		return response()->json(['status'=>false,'error'=>'Please Login to Book an Event']);
    	$eventid = $r->eventid;
    	$user = Auth::User();
    	$tickets = $r->tickets;
    	$ticket_name = $r->ticket_name;
    	$ticket_cost = $r->ticket_cost;
    	$ticket_price = $r->ticket_price;
    }

    public function image_upload(Request $r)
    {
    	// Build the input for validation
        // if($r->url != null && $r->url != "null")
        // {
        //     $url = explode("thetickets.in",$r->url);
        //     $url = $url[1];;
        //     // dd($url);
        //     if(\File::exists($url)) {
        //         \File::delete($url);
        //     }

        // }
        $fileArray = array('image' => $r->import_image);
        $eventid = $r->eventid;
        

        // Tell the validator that this file should be an image
        $rules = array(
          'image' => 'mimes:jpeg,jpg,png,gif|required|max:10240' // max 10MB
        );

        // Now pass the input and rules into the validator
        $validator = \Validator::make($fileArray, $rules);
        // Check to see if validation fails or passes
        if ($validator->fails())
           return response()->json(['status'=>false,'error' => $validator->errors()->getMessages()]);
       


       $image = $r->file('import_image');
        $filename = time(). '.' . $image->getClientOriginalName();
        $r->file('import_image')->move(public_path('uploads'),$filename);

        $url = "https://admin.thetickets.in/uploads/".$filename;
        event::where('event_id',$eventid)->update(['images'=>$url]);
       return response()->json(['status'=>true,'url'=>$url]);

    }

    public function show_image($eventid)
    {
        try {
            $event = event::where('event_id',$eventid)->first();
            return response()->json(['status'=>true,'url'=>$event->images]);
        } catch (\Exception $e) {
            return response()->json(['status'=>false]);
        }
    }






}
