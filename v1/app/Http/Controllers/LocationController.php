<?php

namespace App\Http\Controllers;

use App\Models\MasterProvince;
use App\Models\MasterCity;
use App\Models\MasterDistrict;
use App\Models\MasterVillage;
use App\Models\MasterPostalCode;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
                $country_code = DB::table('website_country')->select('*')
                ->where('subdomain', $request->country_code)
                ->first();

                $transaction = MasterProvince::select('*')
                ->where('id_country', $country_code->id_country)
                ->get();

                $response = [
                    'success'=> true,
                    'message'=> 'Province List',
                    'data' => $transaction
                ];

            }
        }
        return $response;
    }

    public function city(Request $request)
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

                $transaction = MasterCity::select('*')
                ->where('id_province', $request->id_province)
                ->get();

                $response = [
                    'success'=> true,
                    'message'=> 'City List',
                    'data' => $transaction
                ];

            }
        }
        return $response;
    }

    public function district(Request $request)
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

                $transaction = MasterDistrict::select('*')
                ->where('id_city', $request->id_city)
                ->get();

                $response = [
                    'success'=> true,
                    'message'=> 'District List',
                    'data' => $transaction
                ];

            }
        }
        return $response;
    }

    public function subdistrict(Request $request)
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

                $transaction = MasterVillage::select('*')
                ->where('id_district', $request->id_district)
                ->get();

                $response = [
                    'success'=> true,
                    'message'=> 'Village List',
                    'data' => $transaction
                ];

            }
        }
        return $response;
    }

    public function postalcode(Request $request)
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
                if($request->postal_code){
                    $locationBasedOnPostalCode = MasterPostalCode::select(
							'master_postal_code.postal_code',
							'master_village.id_village',
							'master_village.village_name',
							'master_district.id_district',
							'master_district.district_name',
							'master_city.id_city',
							'master_city.id_city_sap',
							'master_city.city_name',
							'master_province.id_province',
							'master_province.id_province_sap',
							'master_province.province_name',
						)
						->join('master_village', 'master_village.id_village', 'master_postal_code.id_village')
						->join('master_district', 'master_district.id_district', 'master_village.id_district')
						->join('master_city', 'master_city.id_city', 'master_district.id_city')
						->join('master_province', 'master_province.id_province', 'master_city.id_province')
						->where('postal_code', $request->postal_code)
						->first();

					$postal_code 		= $locationBasedOnPostalCode->postal_code;
					$id_village     	= $locationBasedOnPostalCode->id_village;
					$village_name     	= $locationBasedOnPostalCode->village_name;
					$id_district    	= $locationBasedOnPostalCode->id_district;
					$district_name     	= $locationBasedOnPostalCode->district_name;
					$id_city        	= $locationBasedOnPostalCode->id_city;
					$id_city_sap    	= $locationBasedOnPostalCode->id_city_sap;
					$city_name    		= $locationBasedOnPostalCode->city_name;
					$id_province    	= $locationBasedOnPostalCode->id_province;
					$id_province_sap    = $locationBasedOnPostalCode->id_province_sap;
					$province_name 		= $locationBasedOnPostalCode->province_name;

					$response = [
						'success'=> true,
						'message'=> 'Postal Code',
						'data' =>
						[
							'postal_code' => $postal_code,
							'id_village' => $id_village,
							'village_name' => $village_name,
							'id_district' => $id_district,
							'district_name' => $district_name,
							'id_city' => $id_city,
							'id_city_sap' => $id_city_sap,
							'city_name' => $city_name,
							'id_province' => $id_province,
							'id_province_sap' => $id_province_sap,
							'province_name' => $province_name,
						]
					];
                }else{
                    $transaction = MasterPostalCode::select('*')
						->where('id_village', $request->id_village)
						->get();

                    $response = [
                        'success'=> true,
                        'message'=> 'Postal Code',
                        'data' => $transaction
                    ];
                }
            }
        }
        return $response;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
