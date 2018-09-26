<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB,Auth;
use App\transaction;

class Bookevents extends Controller
{
    public function book_ticket_booking(Request $r)
    {
        try {
            $eventid = $r->eventid;
            $user = Auth::User();
            $userid = $user->userid;
            if($userid == null)
              return response()->json(['status'=>true]);
            // dd(Auth::User());
            $ticket_name = $r->ticket_name;
            $ticket_max = $r->ticket_max;
            $ticket_price = $r->ticket_price;
            $tableName = $eventid.'_tickets';
            $ticketArray = array();
            $total = 0;
            for($i=0;$i<$r->total;$i++)
            {
              if($ticket_max[$i]!=0)
              {
                $uniqueid = self::generate_unique_id($tableName);
                $now = date('Y-m-d H:i:s');
                DB::table($tableName)->insert(['eventid'=>$eventid,'uniqueid'=>$uniqueid,'userid'=>$userid,'ticket_name'=>$ticket_name[$i],'total_ticket'=>$ticket_max[$i],'cost'=>$ticket_price[$i],'status'=>false , "created_at"=>$now,"updated_at"=>$now]);
               $values['ticket_name'] = $ticket_name[$i];
               $values['total_ticket'] = $ticket_max[$i];
               $values['cost'] = $ticket_price[$i];
               $values['uniqueid'] = $uniqueid;
               array_push($ticketArray, $values);
               $total = $total+$ticket_price[$i]; 
              }
               
            }
              $convenince = (6/100)*$total;
              $total_amount = $convenince+$total;
            return response()->json(['status'=>true,'data'=>$ticketArray ,'total'=>$total ,'convenince'=>$convenince , 'total_amount'=>$total_amount]);    
        
        } catch (\Exception $e) {
          // dd($e);
            return response()->json(['status'=>false]);
        }
    }

    public function store_ticket_booking(Request $r)
    {
      try {
          $eventid = $r->eventid;
          $user = Auth::User();
          $uniqueid = $r->uniqueid;
          $tableName = $eventid.'_tickets';
          $ticketArray = array();
          $transactionid = $r->transactionid;
          for($i=0;$i<count($uniqueid);$i++)
          {
            DB::table($tableName)->where('uniqueid',$uniqueid[$i])->update(['status'=>true]);
          }
          transaction::insert(['transactionid'=>$transactionid,'userid'=>$user->userid,'cost'=>$r->cost,'charge'=>$r->charge,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
          return response()->json(['status'=>true]);    
      
      } catch (\Exception $e) {
          return response()->json(['status'=>true]);
      }
    }

     private static function generate_unique_id($tableName)
    {
        //Create a Random Number 
        $min = 10;
        $max = 1000000;
        $loop=1;
        while($loop)
        {

            //Create a Database Name using the First Word of Oragnization name and inserting random value in middle
            $unique = rand($min,$max);

            //Check whether the Database name exists in the Organization table
            $checkeventId =DB::table($tableName)->where('uniqueid',$unique)->get();

            $loop = (count($checkeventId) == 0)?0:1;

        }
        return $unique;         
    }


}
