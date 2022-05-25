<?php

namespace App\Http\Controllers;

use App\Models\ProductMaster;
use Illuminate\Http\Request;
use DB;
use Storage;
use Response;

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
                    ->where('product_master.id', $id)
                    ->first();

                if(empty($manual_books)){
                    $response = [
                        'success'=> true,
                        'message'=> 'Empty Manual Book',
                        'data' => null
                    ];
                }else{
                    // $file = Storage::disk('manual_sftp')->url($manual_books->path.'/'.$manual_books->file_name);

                    $file = Storage::disk('manual')->url($request->country_code.'/'.$manual_books->file_name);

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
                    ->where('product_master.id', $id)
                    ->first();

                if(empty($manual_books)){
                    $response = [
                        'success'=> true,
                        'message'=> 'Empty Manual Book',
                        'data' => null
                    ];
                }else{
                    $file = Storage::disk('manual')->get($request->country_code.'/'.$manual_books->file_name);

                    return Response($file, 200)->header('Content-Type', $manual_books->mime_type);
                }
            }
        }

        return $response;
    }
}
