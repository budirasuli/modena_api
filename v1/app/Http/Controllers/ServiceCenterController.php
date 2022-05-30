<?php

namespace App\Http\Controllers;


use App\Model\FormServiceCenter;
use App\Models\MasterCity;
use App\Models\MasterPostalCode;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $token = $request->key;
        //dd($token);
        if (is_null($token) OR empty($token) ){
            $response = [
                'success'=> false,
                'message'=> 'List Service'
            ];
            return $response;
           
        }else{
            $api_key = DB::table('api_clients')->select('*')->where('api_token', $token)->first();

            if (!$api_key){
                $response = [
                    'success'=> false,
                    'message'=> 'List Service'
                ];
                return $response; 
            }else{
                // Service List by email or phone
                if(!$request->email)
                {
                     //by phone
                       if($request->phone_number){
                        if(!$request->product_id){
                            $transaction = DB::table('form_service_center')->select('*')
                            ->where('country_code',$request->country_code)
                            ->where('phone',$request->phone_number)
                            ->where('csms_ron_number',$request->csms_ron_number)
                            ->get();
                            $response = [
                                'success'=> true,
                                'message'=> 'List Service By RON Number',
                                'data'=> $transaction
                            ];
                            return $response;
                        }else{
                            $transaction = DB::table('form_service_center')->select('*')
                            ->where('country_code',$request->country_code)
                            ->where('phone',$request->phone_number)
                            ->where('product_id',$request->product_id)
                            ->get();
                            $response = [
                                'success'=> true,
                                'message'=> 'List Service By Product ID',
                                'data'=> $transaction
                            ];
                            return $response;
                        }
                        }else{
                            $transaction = DB::table('form_service_center')->select('*')
                            ->where('country_code',$request->country_code)
                            ->get();
                            $response = [
                                'success'=> true,
                                'message'=> 'List Service By Country',
                                'data'=> $transaction
                            ];
                            return $response;
                        }
                }else{
                    //by product_id
                    if(!$request->product_id){
                        $transaction = DB::table('form_service_center')->select('*')
                        ->where('country_code',$request->country_code)
                        ->where('email',$request->email)
                        ->get();
                        $response = [
                            'success'=> true,
                            'message'=> 'List Service By Email',
                            'data'=> $transaction
                        ];
                        return $response;
                    }else{
                        if(!$request->ron_number){
                            $transaction = DB::table('form_service_center')->select('*')
                            ->where('country_code',$request->country_code)
                            ->where('email',$request->email)
                            ->where('product_id',$request->product_id)
                            ->get();
                            $response = [
                                'success'=> true,
                                'message'=> 'List Service By Product Id',
                                'data'=> $transaction
                            ];
                            return $response;
                        }else{
                            $transaction = DB::table('form_service_center')->select('*')
                            ->where('country_code',$request->country_code)
                            ->where('email',$request->email)
                            ->where('csms_ron_number',$request->csms_ron_number)
                            ->get();
                            $response = [
                                'success'=> true,
                                'message'=> 'List Service By RON Number',
                                'data'=> $transaction
                            ];
                            return $response;
                        }
                    }
                }
            }
        }
        
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
        $token = $request->key;

        if (is_null($token) OR empty($token) ){
            $response = [
                'success'=> false,
                'message'=> 'Cant create Service'

            ];
            return $response;
           
        }else
        {
            $api_key = DB::table('api_clients')->select('*')->where('api_token', $token)->first();

            if (!$api_key){
                $response = [
                    'success'=> false,
                    'message'=> 'API Key Not Valid'
                ];
                return $response; 
            }else
            {
                $rules = [
                    'country_code'      => 'required',
                    'email'              => 'required|email',
                    // 'id_province'        => 'required',
                    // 'id_city'            => 'required',
                    'phone'       => 'required|min:10|numeric',
                    'title_code'         => 'required',
                    'first_name'         => 'required|regex:/^[\pL\s\-]+$/u',
                    'last_name'          => 'required|regex:/^[\pL\s\-]+$/u',
                    'address'            => 'required|min:8',
                    'product_identifier' => 'required',
                    'product_name'       => 'required',
                    'serial_number'      => 'required_if:product_identifier,==,SN',
                    'product_id'         => 'required',
                    'csms_problem_id'    => 'required',
                    'csms_problem_desc'  => 'required',
                    'csms_customer_id'   => 'required',
                    'csms_address_id'    => 'required',
                    'csms_phone_id'      => 'required',
                    'csms_problem_desc'         => 'required',
                    'csms_ron_number'         => 'required',
                    'date'               => 'required',
                ];

                if($request->country_code == 'id') {
                //    $rules ['id_district']    = 'required';
                //    $rules ['id_subdistrict'] = 'required';
                   $rules ['postal_code']    = 'required';
                }
    
                $messages = [
                    'required' => __('Please fill in this field'),
                    'email'    => "Please include an @ in the email address. '" . $request->email . "' is missing an '@'",
                    'regex'    => __('This field contain number')
                ];

                //id location from postalcode

                $village = MasterPostalCode::select('*')
                ->where('postal_code', $request->postal_code)
                ->first();

                $district = DB::table('master_village')
                ->select('id_village','id_district',
                DB::raw("(SELECT DISTINCT(id_city) FROM master_district WHERE id_district=master_village.id_district) as id_city")
                )
                ->where('id_village', $village->id_village)
                ->first();
                
                $province = MasterCity::select('*')
                ->where('id_city', $district->id_city)
                ->first();

                   
                    $id_village     = $district->id_village;
                    $id_district    = $district->id_district;
                    $id_city        = $district->id_city;
                    $id_province    = $province->id_province;
    
                $attributes = [];
    
                $request->validate($rules, $messages, $attributes);
                
                //cek jika sudah ada ron_number
                $cek_ron = DB::table('form_service_center')->select('*')->where('csms_ron_number', $request->csms_ron_number)->first();
                
                if ($cek_ron){
                    $response = [
                        'success'=> false,
                        'message'=> 'RON Number existed',
                        'data'=> $request->csms_ron_number
                    ];
                    return $response;
                }else{
                    DB::table('form_service_center')->insert([
                        'csms_ron_number'    => $request->csms_ron_number,
                        'csms_customer_id'   => $request->csms_customer_id,
                        'csms_address_id'    => $request->csms_address_id,
                        'csms_phone_id'      => $request->csms_phone_id,
                        'csms_problem_id'    => $request->csms_problem_id,
                        'csms_problem_desc'  => $request->csms_problem_desc,
                        'product_identifier' => $request->product_identifier,
                        'product_id'         => $request->product_id,
                        'serial_number'      => ($request->serial_number) ? $request->serial_number : null,
                        'product_name'      => ($request->product_name) ? $request->product_name : null,
                        'country_code'       => $request->country_code,
                        'id_province'        => ($id_province) ? $id_province : null,
                        'id_city'            => ($id_city) ? $id_city : null,
                        'id_district'        => ($id_district) ? $id_district : null,
                        'id_village'         => ($id_village) ? $id_village : null,
                        'postal_code'        => ($request->postal_code) ? $request->postal_code : null,
                        'phone'              => $request->phone,
                        'email'              => $request->email,
                        'title_code'         => $request->title_code,
                        'first_name'         => $request->first_name,
                        'last_name'          => $request->last_name,
                        'address'            => $request->address,
                        'date'               => $request->date,
                        'data_source'        => 'CHAT'

                    ]);
                    
                    $response = [
                        'success'=> true,
                        'message'=> 'Service has been saved !'
                    ];
                    return $response;
                }
             }   
        }
    }
}
