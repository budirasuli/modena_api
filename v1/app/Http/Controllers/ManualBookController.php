<?php

namespace App\Http\Controllers;

use App\Models\ProductMaster;
use Illuminate\Http\Request;
use DB;
use Storage;
use Response;
use File;

class ManualBookController extends Controller
{
    public function index($id, Request $request)
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
                $explodeId = explode('.', $id);
                $id = $explodeId[0];

                $manual_books = ProductMaster::select(
                        'product_master.*',
                        'media.id as media_id',
                        'media.path',
                        'media.file_name',
                        'media.content_type'
                    )
                    ->leftJoin('media', function($join) {
                        $join->on('media.mediable_id', '=', 'product_master.id_product_master_id');
                    })
                    ->where('media.content_type',"LIKE",'manual_id')
                    ->whereNull('product_master.deleted_at')
                    ->where('product_master.country_code', "=", $request->country_code)
                    ->whereNotNull('media.mediable_id')
                    ->where('product_master.sku', $id)
                    ->first();

                if(empty($manual_books)){
                    $response = [
                        'success'=> true,
                        'message'=> 'Empty Manual Book',
                        'data' => null
                    ];
                }else{
					// if(env('APP_ENV') == 'production'){
					// 	$file = Storage::disk('sftp')->url($transaction['path'] . '/' . $transaction['file_name']);
					// }else{
					// 	$sftpImage = Storage::disk('sftp')->get($transaction['path'] . '/' . $transaction['file_name']);
					// 	$filename = File::name($transaction['name']);
					// 	$extension = File::extension($transaction['name']);
					// 	Storage::disk('public')->put("temp/".$filename.'.'.$extension, $sftpImage);
					// 	$transaction['image'] = Storage::disk('public')->url("temp/".$filename.'.'.$extension);
					// }
					$file = Storage::disk('sftp')->url($manual_books->path.'/'.$manual_books->file_name);
					// $filename = File::name($file);
					// $extension = File::extension($file);
					// Storage::disk('public')->put("temp/".$filename.'.'.$extension, $file);
					// $file = Storage::disk('public')->url("temp/".$filename.'.'.$extension);
                    // $file = Storage::disk('sftp')->url($manual_books->path.'/'.$manual_books->file_name);

                    $response = [
                        'success'=> true,
                        'message'=> 'Manual Book',
                        'data' => $file
                    ];
                }
            }
        }

        return $response;
    }

    public function download($id, Request $request)
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
                $explodeId = explode('.', $id);
                $id = $explodeId[0];

                $manual_books = ProductMaster::select(
                        'product_master.*',
                        'media.id as media_id',
                        'media.path',
                        'media.file_name',
                        'media.content_type',
                        'media.mime_type'
                    )
                    ->leftJoin('media', function($join) {
                        $join->on('media.mediable_id', '=', 'product_master.id_product_master_id');
                    })
                    ->where('media.content_type',"LIKE",'manual_id')
                    ->whereNull('product_master.deleted_at')
                    ->where('product_master.country_code', "=", $request->country_code)
                    ->whereNotNull('media.mediable_id')
                    ->where('product_master.sku', $id)
                    ->first();

                if(empty($manual_books)){
                    $response = [
                        'success'=> true,
                        'message'=> 'Empty Manual Book',
                        'data' => null
                    ];
                }else{
                    $file = Storage::disk('sftp')->get($manual_books->path.'/'.$manual_books->file_name);

                    return Response($file, 200)->header('Content-Type', $manual_books->mime_type);
                }
            }
        }

        return $response;
    }
}
