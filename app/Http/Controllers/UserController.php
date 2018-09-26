<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\user;
use Auth;

class UserController extends Controller
{
    // To check whether the Old Password is Correct
    public function check_password(Request $r)
    {
    	$data = array();
    	$userid = $r->userid;
    	$hash_pwd = $r->$OldPassword;
    }
    public function show_profile()
    {
    	try {
    		$user = Auth::User();
	    	$data['user_name'] = $user->name;
	    	$data['email'] = $user->email;
	    	$data['phone'] = $user->phone;
	    	$data['id'] = $user->id;
	    	return response()->json(['status'=>true,'data'=>$data]);	
    	
    	} catch (\Exception $e) {
    		
    		return response()->json(['status'=>false]);
    	}
    	
    }

    public function edit_profile(Request $r)
    {
    	try {
    		$user = User::find($r->id);
	    	$user->name = $r->name;
	    	$user->phone = $r->phone;
	    	$user->save();
	    	return response()->json(['status'=>true,'data'=>$data]);	
    	
    	} catch (\Exception $e) {
    		
    		return response()->json(['status'=>false]);
    	}
    	
    }
}
