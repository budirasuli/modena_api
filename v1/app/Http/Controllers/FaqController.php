<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faq;
use DB;

class FaqController extends Controller
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
                $search = $request->search;

                if(empty($search)){
                    $response = [
                        'success'=> false,
                        'message'=> 'Search key cannot be null'
                    ];
                }else if(strlen($search) < 3){
                    $response = [
                        'success'=> false,
                        'message'=> 'Search key cannot less then 3 words'
                    ];
                }else{
                    $faqs = Faq::where('country_code',$request->country_code)
                        ->where('language_code',$request->language_code)
                        ->where(function($query) use ($search){
                            $query->where('title', 'LIKE', '%'.$search.'%');
                            $query->orWhere('description', 'LIKE', '%'.$search.'%');
                            $query->orWhere('slug', 'LIKE', '%'.$search.'%');
                        })
                        ->get();

                    $response = [
                        'success'=> true,
                        'message'=> 'List FAQ',
                        'data' => $faqs
                    ];
                }
            }
        }

        return $response;
    }
}
