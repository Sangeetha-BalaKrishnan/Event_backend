<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\user;


class UserController extends Controller
{
    // To check whether the Old Password is Correct
    public function check_password(Request $r)
    {
    	$data = array();
    	$userid = $r->userid;
    	$hash_pwd = $r->$OldPassword;

    }
}
