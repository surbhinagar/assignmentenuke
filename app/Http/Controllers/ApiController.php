<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\Models\User;
use App\Models\Imageupload;
use Illuminate\Support\Facades\Validator;
use Image;

class ApiController extends Controller {

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() {

    }


    public function signup(Request $request) {
        
     
       
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = json_decode($validator->errors());
            foreach ($errors as $key => $value) {
                return response()->json(["status" => "flase",'message' => $value[0]],200);

            }
        }

        try {
            $user = $request->all();
          //  $user = User::create($user);
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make($user['password']),
            ]);
           return response()->json(["status" => True,'data' =>$user , 'message' =>'Registered Successfully'],200);
        } catch (Exception $e) {
            $e->getMessage();
            return response()->json(["status" => False, 'message' => 'Something Went Wrong'],500);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function userLogin(Request $request)
    {

        $validator = Validator::make($request->all(), [
            
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = json_decode($validator->errors());
            foreach ($errors as $key => $value) {
                return response()->json(["status" => "flase",'message' => $value[0]],200);

            }
        }
       
           $userdata= User::Where('email',$request->email)->first();
           $password=Hash::check($request->password, $userdata->password);
   
      
        if($password)
        {
            $userdata->token = $userdata->createToken('authToken')->accessToken;
     
 
            return response(["status" => True,'message' => 'Login Successfully','data'=>$userdata->token],200);
           
        }
        else
        {

            return response(["status" => False,'message' => 'Invalid Credential'],200); 
             
        }

    }

    public function imageupload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            
            'image' => 'required|max:500|mimes:jpeg,jpg,png,gif',
            
        ]);
      
        if ($validator->fails()) {
            $errors = json_decode($validator->errors());
            foreach ($errors as $key => $value) {
                return response()->json(["status" => "flase",'message' => $value[0]],200);

            }
        }
        $auth=auth()->user();
       
        if($auth)
        {
            $image = $request->image->store('public/image');
    
            ImageUpload::create([
                'image' => url('/').'/storage/'.$request->image->store('image')
               
            ]);
            return response()->json(['message' => 'Image Add successfully', "status" => TRUE],200);
        }
        else
        {
           return response()->json(['message' => 'Something Went Wrong', "status" => False],500);
        }
    }

}
