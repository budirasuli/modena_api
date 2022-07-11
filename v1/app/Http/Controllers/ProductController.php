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
							->select(
								'product_master.*',
								'product_detail.*',
								DB::raw('IF(size.lenght = 0, NULL, size.lenght) AS size_length'),
								DB::raw('IF(size.width = 0, NULL, size.width) AS size_width'),
								DB::raw('IF(size.height = 0, NULL, size.height) AS size_height'),
								DB::raw('IF(size.depth = 0, NULL, size.depth) AS size_depth'),
								DB::raw('IF(size.thickness = 0, NULL, size.thickness) AS size_thickness'),
								DB::raw('IF(cod.lenght = 0, NULL, cod.lenght) AS cod_length'),
								DB::raw('IF(cod.width = 0, NULL, cod.width) AS cod_width'),
								DB::raw('IF(cod.height = 0, NULL, cod.height) AS cod_height'),
								DB::raw('IF(cod.depth = 0, NULL, cod.depth) AS cod_depth'),
								DB::raw('IF(cod.thickness = 0, NULL, cod.thickness) AS cod_thickness'),
								DB::raw('IF(tcod.lenght = 0, NULL, tcod.lenght) AS tcod_length'),
								DB::raw('IF(tcod.width = 0, NULL, tcod.width) AS tcod_width'),
								DB::raw('IF(tcod.height = 0, NULL, tcod.height) AS tcod_height'),
								DB::raw('IF(tcod.depth = 0, NULL, tcod.depth) AS tcod_depth'),
								DB::raw('IF(tcod.thickness = 0, NULL, tcod.thickness) AS tcod_thickness'),
								DB::raw('IF(ucod.lenght = 0, NULL, ucod.lenght) AS ucod_length'),
								DB::raw('IF(ucod.width = 0, NULL, ucod.width) AS ucod_width'),
								DB::raw('IF(ucod.height = 0, NULL, ucod.height) AS ucod_height'),
								DB::raw('IF(ucod.depth = 0, NULL, ucod.depth) AS ucod_depth'),
								DB::raw('IF(ucod.thickness = 0, NULL, ucod.thickness) AS ucod_thickness'),
								// 'media.*',
								'product_colors.color',
								DB::raw("(SELECT MAX(price) FROM product_detail WHERE id_product_master_id=product_master.id_product_master_id AND country_code=product_master.country_code AND language_code=product_master.language_code) as price"),DB::raw("(SELECT MAX(price)*0.5 FROM product_detail WHERE id_product_master_id=product_master.id_product_master_id AND country_code=product_master.country_code AND language_code=product_master.language_code  AND is_rental = 1 ) as price_rental")
							)
							->leftJoin('product_detail', function($query) use ($request){
								$query->on('product_detail.id_product_master_id', '=', 'product_master.id_product_master_id');
								$query->on('product_detail.country_code', '=', 'product_master.country_code');
								$query->on('product_detail.language_code', '=', 'product_master.language_code');
							})
							->leftJoin('product_sizing AS size', function($query){
								$query->on('product_master.id_product_master_id', '=', 'size.id_product_master_id');
								$query->on('size.sizing_type', '=', DB::raw("'size'"));
							})
							->leftJoin('product_sizing AS cod', function($query){
								$query->on('product_master.id_product_master_id', '=', 'cod.id_product_master_id');
								$query->on('cod.sizing_type', '=', DB::raw("'cod'"));
							})
							->leftJoin('product_sizing AS tcod', function($query){
								$query->on('product_master.id_product_master_id', '=', 'tcod.id_product_master_id');
								$query->on('tcod.sizing_type', '=', DB::raw("'tcod'"));
							})
							->leftJoin('product_sizing AS ucod', function($query){
								$query->on('product_master.id_product_master_id', '=', 'ucod.id_product_master_id');
								$query->on('ucod.sizing_type', '=', DB::raw("'ucod'"));
							})
							->leftJoin('product_colors', function($query) use ($request){
								$query->on('product_colors.id_color', '=', 'product_detail.id_color_id');
								$query->on('product_colors.country_code', '=', 'product_master.country_code');
								$query->on('product_colors.language_code', '=', 'product_master.language_code');
							})
							// ->leftJoin('media', 'product_master.id_product_master_id', '=', 'media.mediable_id')
							// ->where('media.mediable_type', 'App\Model\ProductMasterId')
							// ->where('media.content_type', 'Image')
							->where('product_master.country_code',$request->country_code)
							->where('product_master.language_code',$request->language_code)
							->where('product_detail.is_rental', 1)
							->get()
							->toArray();

						// foreach($transaction as $key => $val){
						// 	if(env('APP_ENV') == 'production'){
						// 		$transaction[$key]->image = Storage::disk('sftp')->url($val->path . '/' . $val->file_name);
						// 	}else{
						// 		$transaction[$key]->image = Storage::disk('sftp')->url($val->path . '/' . $val->file_name);
						// 		// $sftpImage = Storage::disk('sftp')->get($val->path . '/' . $val->file_name);
						// 		// $filename = File::name($val->name);
						// 		// $extension = File::extension($val->name);
						// 		// Storage::disk('public')->put("temp/".$filename.'.'.$extension, $sftpImage);
						// 		// $transaction[$key]->image = Storage::disk('public')->url("temp/".$filename.'.'.$extension);
						// 	}
						// }
					}else{
						$a = $b = $request->term;

						$transaction = DB::table('product_master')
							->select(
								'product_master.*',
								'product_detail.*',
								DB::raw('IF(size.lenght = 0, NULL, size.lenght) AS size_length'),
								DB::raw('IF(size.width = 0, NULL, size.width) AS size_width'),
								DB::raw('IF(size.height = 0, NULL, size.height) AS size_height'),
								DB::raw('IF(size.depth = 0, NULL, size.depth) AS size_depth'),
								DB::raw('IF(size.thickness = 0, NULL, size.thickness) AS size_thickness'),
								DB::raw('IF(cod.lenght = 0, NULL, cod.lenght) AS cod_length'),
								DB::raw('IF(cod.width = 0, NULL, cod.width) AS cod_width'),
								DB::raw('IF(cod.height = 0, NULL, cod.height) AS cod_height'),
								DB::raw('IF(cod.depth = 0, NULL, cod.depth) AS cod_depth'),
								DB::raw('IF(cod.thickness = 0, NULL, cod.thickness) AS cod_thickness'),
								DB::raw('IF(tcod.lenght = 0, NULL, tcod.lenght) AS tcod_length'),
								DB::raw('IF(tcod.width = 0, NULL, tcod.width) AS tcod_width'),
								DB::raw('IF(tcod.height = 0, NULL, tcod.height) AS tcod_height'),
								DB::raw('IF(tcod.depth = 0, NULL, tcod.depth) AS tcod_depth'),
								DB::raw('IF(tcod.thickness = 0, NULL, tcod.thickness) AS tcod_thickness'),
								DB::raw('IF(ucod.lenght = 0, NULL, ucod.lenght) AS ucod_length'),
								DB::raw('IF(ucod.width = 0, NULL, ucod.width) AS ucod_width'),
								DB::raw('IF(ucod.height = 0, NULL, ucod.height) AS ucod_height'),
								DB::raw('IF(ucod.depth = 0, NULL, ucod.depth) AS ucod_depth'),
								DB::raw('IF(ucod.thickness = 0, NULL, ucod.thickness) AS ucod_thickness'),
								// 'media.*',
								'product_colors.color',
								DB::raw("(SELECT MAX(price) FROM product_detail WHERE id_product_master_id=product_master.id_product_master_id AND country_code=product_master.country_code AND language_code=product_master.language_code) as price"),DB::raw("(SELECT MAX(price)*0.5 FROM product_detail WHERE id_product_master_id=product_master.id_product_master_id AND country_code=product_master.country_code AND language_code=product_master.language_code  AND is_rental = 1 ) as price_rental")
							)
							->leftJoin('product_detail', function($query) use ($request){
								$query->on('product_detail.id_product_master_id', '=', 'product_master.id_product_master_id');
								$query->on('product_detail.country_code', '=', 'product_master.country_code');
								$query->on('product_detail.language_code', '=', 'product_master.language_code');
							})
							->leftJoin('product_sizing AS size', function($query){
								$query->on('product_master.id_product_master_id', '=', 'size.id_product_master_id');
								$query->on('size.sizing_type', '=', DB::raw("'size'"));
							})
							->leftJoin('product_sizing AS cod', function($query){
								$query->on('product_master.id_product_master_id', '=', 'cod.id_product_master_id');
								$query->on('cod.sizing_type', '=', DB::raw("'cod'"));
							})
							->leftJoin('product_sizing AS tcod', function($query){
								$query->on('product_master.id_product_master_id', '=', 'tcod.id_product_master_id');
								$query->on('tcod.sizing_type', '=', DB::raw("'tcod'"));
							})
							->leftJoin('product_sizing AS ucod', function($query){
								$query->on('product_master.id_product_master_id', '=', 'ucod.id_product_master_id');
								$query->on('ucod.sizing_type', '=', DB::raw("'ucod'"));
							})
							->leftJoin('product_colors', function($query) use ($request){
								$query->on('product_colors.id_color', '=', 'product_detail.id_color_id');
								$query->on('product_colors.country_code', '=', 'product_master.country_code');
								$query->on('product_colors.language_code', '=', 'product_master.language_code');
							})
							// ->leftJoin('media', 'product_master.id_product_master_id', '=', 'media.mediable_id')
							// ->where('media.mediable_type', 'App\Model\ProductMasterId')
							// ->where('media.content_type', 'Image')
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
							->get()
							->toArray();

						// foreach($transaction as $key => $val){
						// 	if(env('APP_ENV') == 'production'){
						// 		$transaction[$key]->image = Storage::disk('sftp')->url($val->path . '/' . $val->file_name);
						// 	}else{
						// 		$sftpImage = Storage::disk('sftp')->get($val->path . '/' . $val->file_name);
						// 		$filename = File::name($val->name);
						// 		$extension = File::extension($val->name);
						// 		Storage::disk('public')->put("temp/".$filename.'.'.$extension, $sftpImage);
						// 		$transaction[$key]->image = Storage::disk('public')->url("temp/".$filename.'.'.$extension);
						// 	}
						// }
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
								'product_master.*',
								'product_detail.*',
								DB::raw('IF(size.lenght = 0, NULL, size.lenght) AS size_length'),
								DB::raw('IF(size.width = 0, NULL, size.width) AS size_width'),
								DB::raw('IF(size.height = 0, NULL, size.height) AS size_height'),
								DB::raw('IF(size.depth = 0, NULL, size.depth) AS size_depth'),
								DB::raw('IF(size.thickness = 0, NULL, size.thickness) AS size_thickness'),
								DB::raw('IF(cod.lenght = 0, NULL, cod.lenght) AS cod_length'),
								DB::raw('IF(cod.width = 0, NULL, cod.width) AS cod_width'),
								DB::raw('IF(cod.height = 0, NULL, cod.height) AS cod_height'),
								DB::raw('IF(cod.depth = 0, NULL, cod.depth) AS cod_depth'),
								DB::raw('IF(cod.thickness = 0, NULL, cod.thickness) AS cod_thickness'),
								DB::raw('IF(tcod.lenght = 0, NULL, tcod.lenght) AS tcod_length'),
								DB::raw('IF(tcod.width = 0, NULL, tcod.width) AS tcod_width'),
								DB::raw('IF(tcod.height = 0, NULL, tcod.height) AS tcod_height'),
								DB::raw('IF(tcod.depth = 0, NULL, tcod.depth) AS tcod_depth'),
								DB::raw('IF(tcod.thickness = 0, NULL, tcod.thickness) AS tcod_thickness'),
								DB::raw('IF(ucod.lenght = 0, NULL, ucod.lenght) AS ucod_length'),
								DB::raw('IF(ucod.width = 0, NULL, ucod.width) AS ucod_width'),
								DB::raw('IF(ucod.height = 0, NULL, ucod.height) AS ucod_height'),
								DB::raw('IF(ucod.depth = 0, NULL, ucod.depth) AS ucod_depth'),
								DB::raw('IF(ucod.thickness = 0, NULL, ucod.thickness) AS ucod_thickness'),
								// 'media.*',
								'product_colors.color',
								DB::raw("(SELECT MAX(price) FROM product_detail WHERE id_product_master_id=product_master.id_product_master_id AND country_code=product_master.country_code AND language_code=product_master.language_code) as price")
							)
							->leftJoin('product_detail', function($query) use ($request){
								$query->on('product_detail.id_product_master_id', '=', 'product_master.id_product_master_id');
								$query->on('product_detail.country_code', '=', 'product_master.country_code');
								$query->on('product_detail.language_code', '=', 'product_master.language_code');
							})
							->leftJoin('product_sizing AS size', function($query){
								$query->on('product_master.id_product_master_id', '=', 'size.id_product_master_id');
								$query->on('size.sizing_type', '=', DB::raw("'size'"));
							})
							->leftJoin('product_sizing AS cod', function($query){
								$query->on('product_master.id_product_master_id', '=', 'cod.id_product_master_id');
								$query->on('cod.sizing_type', '=', DB::raw("'cod'"));
							})
							->leftJoin('product_sizing AS tcod', function($query){
								$query->on('product_master.id_product_master_id', '=', 'tcod.id_product_master_id');
								$query->on('tcod.sizing_type', '=', DB::raw("'tcod'"));
							})
							->leftJoin('product_sizing AS ucod', function($query){
								$query->on('product_master.id_product_master_id', '=', 'ucod.id_product_master_id');
								$query->on('ucod.sizing_type', '=', DB::raw("'ucod'"));
							})
							->leftJoin('product_colors', function($query) use ($request){
								$query->on('product_colors.id_color', '=', 'product_detail.id_color_id');
								$query->on('product_colors.country_code', '=', 'product_master.country_code');
								$query->on('product_colors.language_code', '=', 'product_master.language_code');
							})
							// ->leftJoin('media', 'product_master.id_product_master_id', '=', 'media.mediable_id')
							// ->where('media.mediable_type', 'App\Model\ProductMasterId')
							// ->where('media.content_type', 'Image')
							->where('product_master.country_code',$request->country_code)
							->where('product_master.language_code',$request->language_code)
							->get()
							->toArray();

						// foreach($transaction as $key => $val){
						// 	if(env('APP_ENV') == 'production'){
						// 		$transaction[$key]->image = Storage::disk('sftp')->url($val->path . '/' . $val->file_name);
						// 	}else{
						// 		$transaction[$key]->image = Storage::disk('sftp')->url($val->path . '/' . $val->file_name);
						// 		// $sftpImage = Storage::disk('sftp')->get($val->path . '/' . $val->file_name);
						// 		// $filename = File::name($val->name);
						// 		// $extension = File::extension($val->name);
						// 		// Storage::disk('public')->put("temp/".$filename.'.'.$extension, $sftpImage);
						// 		// $transaction[$key]->image = Storage::disk('public')->url("temp/".$filename.'.'.$extension);
						// 	}
						// }
					}else{
						$a = $b = $request->term;

						$transaction = DB::table('product_master')
							->select(
								'product_master.*',
								'product_detail.*',
								DB::raw('IF(size.lenght = 0, NULL, size.lenght) AS size_length'),
								DB::raw('IF(size.width = 0, NULL, size.width) AS size_width'),
								DB::raw('IF(size.height = 0, NULL, size.height) AS size_height'),
								DB::raw('IF(size.depth = 0, NULL, size.depth) AS size_depth'),
								DB::raw('IF(size.thickness = 0, NULL, size.thickness) AS size_thickness'),
								DB::raw('IF(cod.lenght = 0, NULL, cod.lenght) AS cod_length'),
								DB::raw('IF(cod.width = 0, NULL, cod.width) AS cod_width'),
								DB::raw('IF(cod.height = 0, NULL, cod.height) AS cod_height'),
								DB::raw('IF(cod.depth = 0, NULL, cod.depth) AS cod_depth'),
								DB::raw('IF(cod.thickness = 0, NULL, cod.thickness) AS cod_thickness'),
								DB::raw('IF(tcod.lenght = 0, NULL, tcod.lenght) AS tcod_length'),
								DB::raw('IF(tcod.width = 0, NULL, tcod.width) AS tcod_width'),
								DB::raw('IF(tcod.height = 0, NULL, tcod.height) AS tcod_height'),
								DB::raw('IF(tcod.depth = 0, NULL, tcod.depth) AS tcod_depth'),
								DB::raw('IF(tcod.thickness = 0, NULL, tcod.thickness) AS tcod_thickness'),
								DB::raw('IF(ucod.lenght = 0, NULL, ucod.lenght) AS ucod_length'),
								DB::raw('IF(ucod.width = 0, NULL, ucod.width) AS ucod_width'),
								DB::raw('IF(ucod.height = 0, NULL, ucod.height) AS ucod_height'),
								DB::raw('IF(ucod.depth = 0, NULL, ucod.depth) AS ucod_depth'),
								DB::raw('IF(ucod.thickness = 0, NULL, ucod.thickness) AS ucod_thickness'),
								// 'media.*',
								'product_colors.color',
								DB::raw("(SELECT MAX(price) FROM product_detail WHERE id_product_master_id=product_master.id_product_master_id AND country_code=product_master.country_code AND language_code=product_master.language_code) as price")
							)
							->leftJoin('product_detail', function($query) use ($request){
								$query->on('product_detail.id_product_master_id', '=', 'product_master.id_product_master_id');
								$query->on('product_detail.country_code', '=', 'product_master.country_code');
								$query->on('product_detail.language_code', '=', 'product_master.language_code');
							})
							->leftJoin('product_sizing AS size', function($query){
								$query->on('product_master.id_product_master_id', '=', 'size.id_product_master_id');
								$query->on('size.sizing_type', '=', DB::raw("'size'"));
							})
							->leftJoin('product_sizing AS cod', function($query){
								$query->on('product_master.id_product_master_id', '=', 'cod.id_product_master_id');
								$query->on('cod.sizing_type', '=', DB::raw("'cod'"));
							})
							->leftJoin('product_sizing AS tcod', function($query){
								$query->on('product_master.id_product_master_id', '=', 'tcod.id_product_master_id');
								$query->on('tcod.sizing_type', '=', DB::raw("'tcod'"));
							})
							->leftJoin('product_sizing AS ucod', function($query){
								$query->on('product_master.id_product_master_id', '=', 'ucod.id_product_master_id');
								$query->on('ucod.sizing_type', '=', DB::raw("'ucod'"));
							})
							->leftJoin('product_colors', function($query) use ($request){
								$query->on('product_colors.id_color', '=', 'product_detail.id_color_id');
								$query->on('product_colors.country_code', '=', 'product_master.country_code');
								$query->on('product_colors.language_code', '=', 'product_master.language_code');
							})
							// ->leftJoin('media', 'product_master.id_product_master_id', '=', 'media.mediable_id')
							// ->where('media.mediable_type', 'App\Model\ProductMasterId')
							// ->where('media.content_type', 'Image')
							->where('product_master.country_code', $request->country_code)
							->where('product_master.language_code', $request->language_code)
							->where(function ($query) use ($a){
								$query->where('product_master.master_name', 'LIKE', '%'.$a.'%')
								->orWhere(function($query2) use ($a) {
									$query2->where(DB::raw("LOWER(product_master.type)"),'like',"%$a%")
										   ->orWhere(DB::raw("LOWER(REPLACE(product_master.type,' ',''))"),'like',"%$a%");
								});
							})
							->get()
							->toArray();

						// foreach($transaction as $key => $val){
						// 	if(env('APP_ENV') == 'production'){
						// 		$transaction[$key]->image = Storage::disk('sftp')->url($val->path . '/' . $val->file_name);
						// 	}else{
						// 		$sftpImage = Storage::disk('sftp')->get($val->path . '/' . $val->file_name);
						// 		$filename = File::name($val->name);
						// 		$extension = File::extension($val->name);
						// 		Storage::disk('public')->put("temp/".$filename.'.'.$extension, $sftpImage);
						// 		$transaction[$key]->image = Storage::disk('public')->url("temp/".$filename.'.'.$extension);
						// 	}
						// }
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
						'product_master.*',
						'product_detail.*',
						DB::raw('IF(size.lenght = 0, NULL, FORMAT(size.lenght, 0)) AS size_length'),
						DB::raw('IF(size.width = 0, NULL, FORMAT(size.width, 0)) AS size_width'),
						DB::raw('IF(size.height = 0, NULL, FORMAT(size.height, 0)) AS size_height'),
						DB::raw('IF(size.depth = 0, NULL, FORMAT(size.depth, 0)) AS size_depth'),
						DB::raw('IF(size.thickness = 0, NULL, FORMAT(size.thickness, 0)) AS size_thickness'),
						DB::raw('IF(cod.lenght = 0, NULL, FORMAT(cod.lenght, 0)) AS cod_length'),
						DB::raw('IF(cod.width = 0, NULL, FORMAT(cod.width, 0)) AS cod_width'),
						DB::raw('IF(cod.height = 0, NULL, FORMAT(cod.height, 0)) AS cod_height'),
						DB::raw('IF(cod.depth = 0, NULL, FORMAT(cod.depth, 0)) AS cod_depth'),
						DB::raw('IF(cod.thickness = 0, NULL, FORMAT(cod.thickness, 0)) AS cod_thickness'),
						DB::raw('IF(tcod.lenght = 0, NULL, FORMAT(tcod.lenght, 0)) AS tcod_length'),
						DB::raw('IF(tcod.width = 0, NULL, FORMAT(tcod.width, 0)) AS tcod_width'),
						DB::raw('IF(tcod.height = 0, NULL, FORMAT(tcod.height, 0)) AS tcod_height'),
						DB::raw('IF(tcod.depth = 0, NULL, FORMAT(tcod.depth, 0)) AS tcod_depth'),
						DB::raw('IF(tcod.thickness = 0, NULL, FORMAT(tcod.thickness, 0)) AS tcod_thickness'),
						DB::raw('IF(ucod.lenght = 0, NULL, FORMAT(ucod.lenght, 0)) AS ucod_length'),
						DB::raw('IF(ucod.width = 0, NULL, FORMAT(ucod.width, 0)) AS ucod_width'),
						DB::raw('IF(ucod.height = 0, NULL, FORMAT(ucod.height, 0)) AS ucod_height'),
						DB::raw('IF(ucod.depth = 0, NULL, FORMAT(ucod.depth, 0)) AS ucod_depth'),
						DB::raw('IF(ucod.thickness = 0, NULL, FORMAT(ucod.thickness, 0)) AS ucod_thickness'),
						'media.path',
						'media.file_name',
						'media.name as media_name',
						'product_colors.color',
						DB::raw("(SELECT MAX(price) * 0.5 FROM product_detail WHERE id_product_master_id=product_master.id_product_master_id AND country_code=product_master.country_code AND language_code=product_master.language_code AND is_rental = 1
						) as price_rental")
					)
					->leftJoin('product_detail', function($query) use ($request){
						$query->on('product_detail.id_product_master_id', '=', 'product_master.id_product_master_id');
						$query->on('product_detail.country_code', '=', 'product_master.country_code');
						$query->on('product_detail.language_code', '=', 'product_master.language_code');
					})
					->leftJoin('product_sizing AS size', function($query){
						$query->on('product_master.id_product_master_id', '=', 'size.id_product_master_id');
						$query->on('size.sizing_type', '=', DB::raw("'size'"));
					})
					->leftJoin('product_sizing AS cod', function($query){
						$query->on('product_master.id_product_master_id', '=', 'cod.id_product_master_id');
						$query->on('cod.sizing_type', '=', DB::raw("'cod'"));
					})
					->leftJoin('product_sizing AS tcod', function($query){
						$query->on('product_master.id_product_master_id', '=', 'tcod.id_product_master_id');
						$query->on('tcod.sizing_type', '=', DB::raw("'tcod'"));
					})
					->leftJoin('product_sizing AS ucod', function($query){
						$query->on('product_master.id_product_master_id', '=', 'ucod.id_product_master_id');
						$query->on('ucod.sizing_type', '=', DB::raw("'ucod'"));
					})
					->leftJoin('product_colors', function($query) use ($request){
						$query->on('product_colors.id_color', '=', 'product_detail.id_color_id');
						$query->on('product_colors.country_code', '=', 'product_master.country_code');
						$query->on('product_colors.language_code', '=', 'product_master.language_code');
					})
					->leftJoin('media', 'product_master.id_product_master_id', '=', 'media.mediable_id')
					->where('media.mediable_type', 'App\Model\ProductMasterId')
					->where('media.content_type', 'Image')
					->where('product_master.country_code',$request->country_code)
					->where('product_master.language_code',$request->language_code)
					->where('product_master.type', $request->model)
					->first();

				if(!empty($transaction)){
					$transaction->toArray();

                    if(env('APP_ENV') == 'production'){
						$transaction['image'] = Storage::disk('sftp')->url($transaction['path'] . '/' . $transaction['file_name']);
					}else{
						$sftpImage = Storage::disk('sftp')->get($transaction['path'] . '/' . $transaction['file_name']);
						$filename = File::name($transaction['media_name']);
						$extension = File::extension($transaction['media_name']);
						Storage::disk('public')->put("temp/".$filename.'.'.$extension, $sftpImage);
						$transaction['image'] = Storage::disk('public')->url("temp/".$filename.'.'.$extension);
					}

				}

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
                ->leftJoin('product_detail', function($query) use ($request){
					$query->on('product_detail.id_product_master_id', '=', 'product_master.id_product_master_id');
					$query->on('product_detail.country_code', '=', 'product_master.country_code');
					$query->on('product_detail.language_code', '=', 'product_master.language_code');
				})
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
