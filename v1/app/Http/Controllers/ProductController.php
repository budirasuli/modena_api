<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
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
                'message'=> 'List Product'
            ];
            return $response;
        }else{
            $api_key = DB::table('api_clients')->select('*')->where('api_token', $token)->first();

            if (!$api_key){
                $response = [
                    'success'=> false,
                    'message'=> 'List Product'
                ];
                return $response;
            }else{
                $transaction = DB::table('product_master')->select('*', DB::raw("(SELECT MAX(price) FROM product_detail WHERE id_product_master_id=product_master.id_product_master_id AND country_code=product_master.country_code AND language_code=product_master.language_code) as price"))
                ->where('country_code',$request->country_code)
                ->where('language_code',$request->language_code)
                ->get();
                $response = [
                    'success'=> true,
                    'message'=> 'List Product',
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
    public function detail(Request $request)
    {

        $token = $request->key;
        //  dd($token);
        if (is_null($token) OR empty($token) ){
            $response = [
                'success'=> false,
                'message'=> 'Detail Product'
            ];
            return $response;

        }else{
            $api_key = DB::table('api_clients')->select('*')->where('api_token', $token)->first();

            if (!$api_key){
                $response = [
                    'success'=> false,
                    'message'=> 'Detail Product'
                ];
                return $response;
            }else{
                $transaction = Product::select('*')
                ->leftJoin('product_detail', 'product_master.id_product_master_id', '=', 'product_detail.id_product_master_id')
                ->leftJoin('media', 'product_master.id_product_master_id', '=', 'media.mediable_id')
                ->where('media.mediable_type', 'App\Model\ProductMasterId')
                ->where('product_master.country_code',$request->country_code)
                ->where('product_master.language_code',$request->language_code)
                ->where('product_master.type', $request->model)
                ->first();
                $response = [
                    'success'=> true,
                    'message'=> 'Detail Product',
                    'data'=> $transaction
                ];
                return $response;
            }
        }
    }

    public function sku(Request $request)
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
                    'message'=> 'List Product'
                ];
                return $response;
            }else{
                $transaction = Product::select('*')
                ->leftJoin('product_detail', 'product_master.id_product_master_id', '=', 'product_detail.id_product_master_id')
                ->leftJoin('media', 'product_master.id_product_master_id', '=', 'media.mediable_id')
                ->where('media.mediable_type', 'App\Model\ProductMasterId')
                ->where('product_master.country_code',$request->country_code)
                ->where('product_master.language_code',$request->language_code)
                ->where('product_master.sku', $request->sku)
                ->first();
                $response = [
                    'success'=> true,
                    'message'=> 'List Product',
                    'data'=> $transaction
                ];
                return $response;
            }
        }
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
