<?php

namespace App\Http\Controllers;


use App\Model\FormServiceCenter;
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
                // Service List
                if(!$request->email)
                {
                    $transaction = DB::table('form_service_center')->select('*')
                    ->where('country_code',$request->country_code)
                    ->get();
                    $response = [
                        'success'=> true,
                        'message'=> 'List Service',
                        'data'=> $transaction
                    ];
                    return $response;
                }else{
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
        //dd($token);
        if (is_null($token) OR empty($token) ){
            $response = [
                'success'=> false,
                'message'=> 'Create Service'
            ];
            return $response;
           
        }else
        {
            $api_key = DB::table('api_clients')->select('*')->where('api_token', $token)->first();

            if (!$api_key){
                $response = [
                    'success'=> false,
                    'message'=> 'Add Service'
                ];
                return $response; 
            }else
            {

                $rules = [
                    'email'              => 'required|email',
                    'phone'              => 'required|min:10|numeric',
                    'title_code'         => 'required',
                    'first_name'         => 'required|regex:/^[\pL\s\-]+$/u',
                    'last_name'          => 'required|regex:/^[\pL\s\-]+$/u',
                    'address'            => 'required|min:8',
                    'id_province'        => 'required',
                    'province'           => 'required',
                    'id_city'            => 'required',
                    'city'               => 'required',
                    'product_identifier' => 'required',
                    'product_name'       => 'required',
                    'series_number'      => 'required_if:product_identifier,==,SN',
                    'product_id'         => 'required',
                    'id_problem'         => 'required',
                    'problem'            => 'required',
                    'date'               => 'required',
                    // 'time'            => 'required',
                    // 'image'           => 'required|image',
                    // 'invoice'         => 'mimes:jpg,jpeg,png,svg,doc,docx,pdf'
                ];
    
                if($request->country_code == 'id') {
                   $rules ['district_id']    = 'required';
                   $rules ['district']       = 'required';
                   $rules ['subdistrict_id'] = 'required';
                   $rules ['subdistrict']    = 'required';
                   $rules ['postal_code_id'] = 'required';
                   $rules ['postal_code']    = 'required';
                }
    
                $messages = [
                    'required' => __('Please fill in this field'),
                    'email'    => "Please include an @ in the email address. '" . $request->email . "' is missing an '@'",
                    'regex'    => __('This field contain number')
                ];
    
                $attributes = [];
    
                $request->validate($rules, $messages, $attributes);
    
                $response = [
                        'success'=> true,
                        'message'=> 'Book a Service',
                        'data'=> $request
                    ];
                return $request;
             }
            
        }
        
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
