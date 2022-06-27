<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductMaster;
use Illuminate\Support\Facades\DB;
use Storage;
use File;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $token = $request->key;

        if (is_null($token) OR empty($token) ){
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
                if($request->is_rental){
					if(empty($request->term)){
						$transaction = DB::table('product_master')
							->select(
								'*',
								DB::raw("(SELECT MAX(price) FROM product_detail WHERE id_product_master_id=product_master.id_product_master_id AND country_code=product_master.country_code AND language_code=product_master.language_code) as price"),DB::raw("(SELECT MAX(price)*0.5 FROM product_detail WHERE id_product_master_id=product_master.id_product_master_id AND country_code=product_master.country_code AND language_code=product_master.language_code  AND is_rental = 1 ) as price_rental")
							)
							->leftJoin('product_detail', 'product_master.id_product_master_id', '=', 'product_detail.id_product_master_id')
							->where('product_master.country_code',$request->country_code)
							->where('product_master.language_code',$request->language_code)
							->where('product_detail.is_rental', 1)
							->get();
					}else{
						$a = $b = $request->term;

						$transaction = DB::table('product_master')
							->select(
								'*',
								DB::raw("(SELECT MAX(price) FROM product_detail WHERE id_product_master_id=product_master.id_product_master_id AND country_code=product_master.country_code AND language_code=product_master.language_code) as price"),DB::raw("(SELECT MAX(price)*0.5 FROM product_detail WHERE id_product_master_id=product_master.id_product_master_id AND country_code=product_master.country_code AND language_code=product_master.language_code  AND is_rental = 1 ) as price_rental")
							)
							->leftJoin('product_detail', 'product_master.id_product_master_id', '=', 'product_detail.id_product_master_id')
							->where('product_master.country_code',$request->country_code)
							->where('product_master.language_code',$request->language_code)
							->where(function ($query) use ($a){
								$query->where('product_master.master_name', 'LIKE', '%'.$a.'%')
								->orWhere(function($query2) use ($a) {
									$query2->where(DB::raw("LOWER(product_master.type)"),'like',"%$a%")
										   ->orWhere(DB::raw("LOWER(REPLACE(product_master.type,' ',''))"),'like',"%$a%");
								});
							})
							->where('product_detail.is_rental', 1)
							->get();
					}

                    $response = [
                        'success'=> true,
                        'message'=> 'List Product Rental',
                        'data'=> $transaction
                    ];

                    return $response;
                }else{
					if(empty($request->term)){
						$transaction = DB::table('product_master')
							->select(
								'*',
								DB::raw("(SELECT MAX(price) FROM product_detail WHERE id_product_master_id=product_master.id_product_master_id AND country_code=product_master.country_code AND language_code=product_master.language_code) as price")
							)
							->where('country_code',$request->country_code)
							->where('language_code',$request->language_code)
							->get();
					}else{
						$a = $b = $request->term;

						$transaction = DB::table('product_master')
							->select(
								'*',
								DB::raw("(SELECT MAX(price) FROM product_detail WHERE id_product_master_id=product_master.id_product_master_id AND country_code=product_master.country_code AND language_code=product_master.language_code) as price")
							)
							->where('country_code',$request->country_code)
							->where('language_code',$request->language_code)
							->where(function ($query) use ($a){
								$query->where('product_master.master_name', 'LIKE', '%'.$a.'%')
								->orWhere(function($query2) use ($a) {
									$query2->where(DB::raw("LOWER(product_master.type)"),'like',"%$a%")
										   ->orWhere(DB::raw("LOWER(REPLACE(product_master.type,' ',''))"),'like',"%$a%");
								});
							})
							->get();
					}

                    $response = [
                        'success'=> true,
                        'message'=> 'List Product Non Rental',
                        'data'=> $transaction
                    ];
                    return $response;
                }

            }
        }

		return $response;
    }

    public function product_category(Request $request)
    {
        $token = $request->key;

        if (is_null($token) OR empty($token) ){
            $response = [
                'success'=> false,
                'message'=> 'Token cannot be null'
            ];
            return $response;
        }else{
            $api_key = DB::table('api_clients')->select('*')->where('api_token', $token)->first();

            if (!$api_key){
                $response = [
                    'success'=> false,
                    'message'=> 'Token mismatch'
                ];
                return $response;
            }else
            {
                $transaction = DB::table('product_category')->select('*')
                ->where('country_code',$request->country_code)
                ->where('language_code',$request->language_code)
                ->where('active', 1)
                ->get();
                $response = [
                    'success'=> true,
                    'message'=> 'List Product Category',
                    'data'=> $transaction
                ];
                return $response;

            }
        }
    }

	public function product_sub_category(Request $request)
	{
		$token = $request->key;

        if (is_null($token) OR empty($token) ){
            $response = [
                'success'=> false,
                'message'=> 'Token cannot be null'
            ];
        }else{
			$api_key = DB::table('api_clients')->select('*')->where('api_token', $token)->first();

            if(!$api_key){
                $response = [
                    'success'=> false,
                    'message'=> 'Token mismatch'
                ];
			}else{
				$subProducts = DB::table('product_sub_category')
					->select('*')
                	->where('country_code', $request->country_code)
                	->where('language_code', $request->language_code)
                	->where('active', 1)
                	->get();

                $response = [
                    'success'=> true,
                    'message'=> 'List Product Sub Category',
                    'data'=> $subProducts
                ];
			}
		}

		return $response;
	}

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
                $transaction = Product::select(
						'*',
						DB::raw("(SELECT MAX(price) * 0.5 FROM product_detail WHERE id_product_master_id=product_master.id_product_master_id AND country_code=product_master.country_code AND language_code=product_master.language_code AND is_rental = 1
						) as price_rental")
					)
					->leftJoin('product_detail', 'product_master.id_product_master_id', '=', 'product_detail.id_product_master_id')
					->leftJoin('media', 'product_master.id_product_master_id', '=', 'media.mediable_id')
					->where('media.mediable_type', 'App\Model\ProductMasterId')
					->where('product_master.country_code',$request->country_code)
					->where('product_master.language_code',$request->language_code)
					->where('product_master.type', $request->model)
					->first()
					->toArray();

				$sftpImage = Storage::disk('sftp')->get($transaction['path'] . '/' . $transaction['file_name']);
				dd($sftpImage);
				$filename = File::name($transaction['name']);
				$extension = File::extension($transaction['name']);
				Storage::disk('public')->put("temp/".$filename.'.'.$extension, $sftpImage);
				$transaction['image'] = Storage::disk('public')->url("temp/".$filename.'.'.$extension);

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
