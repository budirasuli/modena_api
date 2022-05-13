<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $token = $request->key;
        //  dd($token);
        if (is_null($token) OR empty($token) ){
            $response = [
                'success'=> false,
                'message'=> 'List Product'
            ];
            return $response;
           
        }else{
            $api_key = DB::table('api_clients')->select('*')->where('api_token', $token)->first();

            if (!$api_key){
                $response = [
                    'success'=> false,
                    'message'=> 'List Address'
                ];
                return $response; 
            }else{
                $transaction = Address::select('*')
                ->where('model_id',$request->customer_id)
                ->get();
                $response = [
                    'success'=> true,
                    'message'=> 'List Address',
                    'data'=> $transaction
                ];
                return $response;
            }
        }
    }

    public function address_detail(Request $request)
    {
        $token = $request->key;
        //  dd($token);
        if (is_null($token) OR empty($token) ){
            $response = [
                'success'=> false,
                'message'=> 'List Product'
            ];
            return $response;
           
        }else{
            $api_key = DB::table('api_clients')->select('*')->where('api_token', $token)->first();

            if (!$api_key){
                $response = [
                    'success'=> false,
                    'message'=> 'Detail Address'
                ];
                return $response; 
            }else{
                $transaction = Address::select('id','model_id as customer_id', 'title', 'province_id','city_id','district_id','subdistrict_id','postal_code','latitude','longitude','phone_number','mobile_number','province_name','city_name','district_name','subdistrict_name','address')
                ->where('model_id',$request->customer_id)
                ->where('id',$request->address_id)
                
                ->get();
                $response = [
                    'success'=> true,
                    'message'=> 'List Address',
                    'data'=> $transaction
                ];
                return $response;
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
