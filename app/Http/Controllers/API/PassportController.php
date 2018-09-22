<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator , DB;

class PassportController extends Controller
{
    public $successStatus = 200;

    /**
		Login Details
    **/
		public function login()
		{
			try
			{
				if(Auth::attempt(['email' => request('email'), 'password' => request('password')]))
				{
					$user = Auth::user();
					// dd($user);
					$success['token']= $user->createToken('MyApp')->accessToken;
					return response()->json(['status' => true ,'token'=>$success['token'],'name'=>$user->name,'userid'=>$user->userid]);
				}
				else
				{
					return response()->json(['status' => false,'error'=>'Please check your username or password' ]);
				}	
			}
			catch(\Exception $e)
			{
				return response()->json(['status'=>false,'error'=>'Your username or password is incorrect']);
			}
			
		}

		public function register(Request $request)
		{
			// return "gl";
			try{
				$validator = Validator::make($request->all(),[
					'name' => 'required',
					'email' => 'required|email',
					'password' => 'required',
					'role' => 'required',
					'phone' => 'required',
				]);

				if($validator->fails())
				{
					return response()->json(['error' => $validator->error()],401);
				}

				$input = $request->all();
				$input['password'] = bcrypt($input['password']);
				$userid = self::generate_user_id($request->name);
				$input['userid'] = $userid;
				// dd($input);
				$user = User::create(['userid'=>$request->userid,'name'=>$request->name , 'email'=>$request->email ,'password'=>bcrypt($request->password) ,'role'=>$request->role ,'phone'=>$request->phone]);
				DB::table('users')->where('email',$request->email)->update(['userid'=>$userid,'role'=>$request->role,'phone'=>$request->phone]);
				$success['token'] = $user->createToken('MyApp')->accessToken;
				$success['name'] = $user->name;
				return response()->json(['status'=>true,'token' => $success['token'],'name'=>$user->name,'userid'=>$userid] , $this->successStatus);	
			}
			catch(\Illuminate\Database\QueryException $e)
	        {
	            $errorCode = $e->errorInfo[1];
	            if($errorCode == 1062){
	                return response()->json(['status'=>false,'error'=>'The email id already exists'],200);
	            }
	        }
	        catch(\Exception $e)
	        {
	        	return response()->json(['status'=>false , 'error'=>'User not created . Try again after some time.']);
	        }
			
		}

		public function getDetails()
		{
			$user = Auth::User();
			return response()->json(['success' => $user] , $this->successStatus);
		}


	private static function generate_user_id($userName)
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
            $userId = strtolower(substr($userName,0,1).$randomValue.substr($userName,-1));

            //Check whether the Database name exists in the Organization table
            $checkUserId =User::where('userid',$userId)->get();

            $loop = (count($checkUserId) == 0)?0:1;

        }
        return $userId;         
    }
}
