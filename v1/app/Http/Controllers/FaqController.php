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
                    $search = explode(' ', $search);

                    $faqs = Faq::select('id', 'title')
                        ->where('country_code',$request->country_code)
                        ->where('language_code',$request->language_code)
                        ->where(function($query) use ($search){
                            foreach($search as $key => $val){
                                $query->where('title', 'LIKE', '%'.$val.'%');
                                $query->orWhere('description', 'LIKE', '%'.$val.'%');
                                $query->orWhere('slug', 'LIKE', '%'.$val.'%');
                            }
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

    public function faq_detail($id, Request $request)
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
                if(empty($id)){
                    $response = [
                        'success'=> false,
                        'message'=> 'FAQ ID cannot be null'
                    ];
                }else{
                    $faq = Faq::where('id', $id)
                        ->first();

                    $faq['description'] = strip_tags($faq['description']);

                    $response = [
                        'success'=> true,
                        'message'=> 'Detail FAQ',
                        'data' => $faq
                    ];
                }
            }
        }

        return $response;
    }
}
