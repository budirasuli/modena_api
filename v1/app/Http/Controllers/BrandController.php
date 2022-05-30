<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterBrand;
use DB;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $token = $request->key;

        if (is_null($token) || empty($token)){
            $response = [
                'success'=> false,
                'message'=> 'Token cannot be null'
            ];
        }else{
            $api_key = DB::table('api_clients')->select('*')->where('api_token', $token)->first();

            if (!$api_key){
                $response = [
                    'success'=> false,
                    'message'=> 'Token mismatch'
                ];
            }else{
                $brands = MasterBrand::get();

                if(empty($brands)){
                    $response = [
                        'success'=> true,
                        'message'=> 'Empty Brand',
                        'data' => null
                    ];
                }else{
                    $response = [
                        'success'=> true,
                        'message'=> 'Brand',
                        'data' => $brands
                    ];
                }
            }
        }

        return $response;
    }
}
