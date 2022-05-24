<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class UserRegisterController extends Controller
{
    
    public function index(Request $request)
    {
        $token = $request->key;
        //dd($token);
        if (is_null($token) OR empty($token) ){
            $response = [
                'success'=> false,
                'message'=> 'Token cannot be null'
            ];
            return $response;
        }else
        {
            $api_key = DB::table('api_clients')->select('*')->where('api_token', $token)->first();

            if (!$api_key){
                $response = [
                    'success'=> false,
                    'message'=> 'Token mismatch'
                ];
                return $response;
            }else
            {
                    $transaction = User::select('*')
                    ->where('email',$request->email)
                    ->get();
                    $response = [
                        'success'=> true,
                        'message'=> 'List User',
                        'data'=> $transaction
                    ];
                    return $response;
            }
        }
    }
    public function register(Request $request)
    {
  
        $request->validate([
            'email' => ['requred|unique|users|email'],
            'password' => ['required|min:6'],
            
        ]);

        $email = $request->email;
        $password = Hash::make($request->password);

        $user = User::create([
            'email' => $email,
            'password' => $password
        ]);

        if ($user){
            $userid= User::find('id')->first;
            $response = [
                'success'=> true,
                'message'=> 'Register Succesfuly',
                'User Id' => $userid
        
            ];
            return $response;
        }else{
            $response = [
                'success'=> false,
                'message'=> 'Register Failed',
        
            ];
            return $response;
        }
    
    }
}
