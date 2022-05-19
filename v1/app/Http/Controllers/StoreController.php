<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreOnline;
use App\Models\StoreOfficial;
use DB;

class StoreController extends Controller
{
    public function online_store(Request $request)
    {
        $token = $request->key;
        $country_code = $request->country_code;

        if (is_null($token) || empty($token)){
            $response = [
                'success'=> false,
                'message'=> 'Token cannot be null'
            ];
        }else if (is_null($country_code) || empty($country_code)){
            $response = [
                'success'=> false,
                'message'=> 'Country code cannot be null'
            ];
        }else{
            $api_key = DB::table('api_clients')->select('*')->where('api_token', $token)->first();

            if (!$api_key){
                $response = [
                    'success'=> false,
                    'message'=> 'Token mismatch'
                ];
            }else{
                $storeOnline = StoreOnline::select('link', 'name')
                    ->where('country_code', $country_code)
                    ->get();

                $response = [
                    'success'=> true,
                    'message'=> 'List Online Store',
                    'data' => $storeOnline
                ];
            }
        }

        return $response;
    }

    public function store_location(Request $request)
    {
        $token = $request->key;
        $country_code = $request->country_code;
        $longatitude = $request->longatitude;
        $latitude = $request->latitude;
        $search = $request->search;

        if (is_null($token) || empty($token)){
            $response = [
                'success' => false,
                'message' => 'Token cannot be null'
            ];
        }else if(empty($country_code)){
            $response = [
                'success' => false,
                'message' => 'Country code cannot be null'
            ];
        }else{
            $api_key = DB::table('api_clients')->select('*')->where('api_token', $token)->first();

            if (!$api_key){
                $response = [
                    'success' => false,
                    'message' => 'Token mismatch'
                ];
            }else{
                if(!empty($longatitude) || !empty($latitude)  || !empty($search)){
                    if(is_numeric($longatitude) || is_numeric($latitude)){
                        $search = explode(' ', $search);

                        $storeOfficial = StoreOfficial::select(
                            'store_official.id',
                            'store_official.id_store_category_id',
                            'store_name as name',
                            'store_official.country_code',
                            'id_province_id as province_id',
                            'id_city_id as city_id',
                            'store_address as address',
                            'google_address',
                            'longitude',
                            'latitude',
                            'phone_area',
                            'phone',
                            DB::raw("TRUNCATE((6371 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longatitude)) + sin(radians($latitude)) * sin(radians(latitude)))), 2) AS distance")
                        )
                        ->join('store_category', function($query){
                            $query->on('store_category.id_store_category_id', '=', 'store_official.id_store_category_id');
                            $query->on('store_category.country_code', '=', 'store_official.country_code');
                        })
                        ->where(function($query) use ($search){
                            foreach($search as $key => $val){
                                $query->where('store_official.store_name', 'LIKE', '%'.$val.'%');
                                $query->orWhere('store_official.store_address', 'LIKE', '%'.$val.'%');
                                $query->orWhere('store_official.google_address', 'LIKE', '%'.$val.'%');
                            }
                        })
                        ->where('store_official.country_code', $country_code)
                        ->where('store_official.active', 1)
                        ->orderBy('distance', 'ASC')
                        ->limit(10)
                        ->get();

                        $response = [
                            'success'=> true,
                            'message'=> 'List Official Store',
                            'data' => $storeOfficial
                        ];
                    }else{
                        $response = [
                            'success' => false,
                            'message' => 'Latitude and Longatitude must be numeric'
                        ];
                    }
                }else if(!empty($search)){
                    $search = explode(' ', $search);

                    $storeOfficial = StoreOfficial::select(
                        'store_official.id',
                        'store_official.id_store_category_id',
                        'store_name as name',
                        'store_official.country_code',
                        'id_province_id as province_id',
                        'id_city_id as city_id',
                        'store_address as address',
                        'google_address',
                        'longitude',
                        'latitude',
                        'phone_area',
                        'phone',
                    )
                    ->join('store_category', function($query){
                        $query->on('store_category.id_store_category_id', '=', 'store_official.id_store_category_id');
                        $query->on('store_category.country_code', '=', 'store_official.country_code');
                    })
                    ->where('store_official.country_code', $country_code)
                    ->where('store_official.active', 1)
                    ->where(function($query) use ($search){
                        foreach($search as $key => $val){
                            $query->where('store_official.store_name', 'LIKE', '%'.$val.'%');
                            $query->orWhere('store_official.store_address', 'LIKE', '%'.$val.'%');
                            $query->orWhere('store_official.google_address', 'LIKE', '%'.$val.'%');
                        }
                    })
                    ->get();

                    $response = [
                        'success'=> true,
                        'message'=> 'List Official Store',
                        'data' => $storeOfficial
                    ];
                }else if(!empty($longatitude) || !empty($latitude)){
                    if(is_numeric($longatitude) || is_numeric($latitude)){
                        $storeOfficial = StoreOfficial::select(
                            'store_official.id',
                            'store_official.id_store_category_id',
                            'store_name as name',
                            'store_official.country_code',
                            'id_province_id as province_id',
                            'id_city_id as city_id',
                            'store_address as address',
                            'google_address',
                            'longitude',
                            'latitude',
                            'phone_area',
                            'phone',
                            DB::raw("TRUNCATE((6371 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longatitude)) + sin(radians($latitude)) * sin(radians(latitude)))), 2) AS distance")
                        )
                        ->join('store_category', function($query){
                            $query->on('store_category.id_store_category_id', '=', 'store_official.id_store_category_id');
                            $query->on('store_category.country_code', '=', 'store_official.country_code');
                        })
                        ->where('store_official.country_code', $country_code)
                        ->where('store_official.active', 1)
                        ->orderBy('distance', 'ASC')
                        ->limit(10)
                        ->get();

                        $response = [
                            'success'=> true,
                            'message'=> 'List Official Store',
                            'data' => $storeOfficial
                        ];
                    }else{
                        $response = [
                            'success' => false,
                            'message' => 'Latitude and Longatitude must be numeric'
                        ];
                    }
                }
            }
        }

        return $response;
    }
}
