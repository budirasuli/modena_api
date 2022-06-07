<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\ProductRegistration;
use App\Models\MasterCity;
use App\Models\MasterPostalCode;

class ProductRegistrationController extends Controller
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
				if(!empty($request->phone) && !empty($request->email) && !empty($request->country_code)){
					$productRegistrations = ProductRegistration::select('*')
						->with('user')
						->with('invoice')
						->with('warranty')
						->where('country_code', $request->country_code)
						->where('phone', $request->phone)
						->where('email', $request->email)
						->get();

					$response = [
						'success'=> true,
						'message'=> 'List Registration',
						'data'=> $productRegistrations
					];
				}else{
					$response = [
                        'success' => false,
                        'message' => 'Filter cannot be null'
                    ];
				}
            }
        }
		return $response;
    }

    public function store(Request $request)
    {
        $token = $request->key;
        //dd($token);
        if (is_null($token) OR empty($token) ){
            $response = [
                'success'=> false,
                'message'=> 'Token cannot be null'
            ];
            return $response;
        }else
        {
            $api_key = DB::table('api_clients')->select('*')->where('api_token', $token)->first();
            if (!$api_key){
                $response = [
                    'success'=> false,
                    'message'=> 'Token mismatch'
                ];
                return $response;
            }else{
                $request->validate([
                    'first_name' => ['required'],
                    'last_name' => ['required'],
                    'address' => ['required'],
                    // 'id_province' => ['required'],
                    // 'id_city' => ['required'],
                    'email' => ['required', 'email'],
                    'phone' => ['required','min:8'],
                    'product_brand' => ['required'],
                    'product_name' => ['required'],
                    'serial_number' => ['required'],
                    'purchase_date' => ['required', 'date'],
                    'store_name' => ['required'],
                    'invoice' => ['required', 'mimes:jpg,jpeg,png,svg,pdf'],
                    'warranty' => ['required', 'mimes:jpg,jpeg,png,svg,pdf'],

                ]);
                DB::beginTransaction();
                try {

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

                    $data = new ProductRegistration();
                    $insReg  = [
                        'user_id' => $request->user_id,
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'address' => $request->address,
                        'province_id' => $request->id_province,
                        'city_id' => $request->id_city,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'brand' => $request->product_brand,
                        'product_id' => $request->product_id,
                        'category_id' => $request->category_id,
                        'product_name' => $request->product_name,
                        'serial_number' => $request->serial_number,
                        'purchase_date' => $request->purchase_date,
                        'store_name' => $request->store_name,
                        'country_code' => $request->country_code,
                        'language_code' => $request->language_code,
                        'data_source'          => 'CHAT',
                    ];


                    //$data->user_id = Auth::id();
                    $data->fill($insReg);
                    $data->save();
                    if ($request->file('invoice')) {
                        $this->storeFile($request->file('invoice'), $data, 'invoice', "invoices/{$data->id}", 'invoice');
                    }
                    if ($request->file('warranty')) {
                        $this->storeFile($request->file('warranty'), $data, 'warranty', "warranties/{$data->id}", 'warranty');
                    }
                    DB::commit();
                    $response = [
                        'success'=> true,
                        'message'=> 'Your information has been saved, please wait for a reply from our customer service'

                    ];
                    return $response;

                } catch (\Exception $e) {

                    DB::rollback();
                    throw $e;
                    $response = [
                        'success'=> false,
                        'message'=> 'Insert data failed'

                    ];
                }
            }
        }
    }

    protected function storeFile($file, $model, $relation, $path, $content_type = null)
    {
        $document = $file;
        $fileName = $document->hashName();

        $data = [
            'content_type' => $content_type,
            'name' => $document->getClientOriginalName(),
            'path' => $path,
            'file_name' => $fileName,
            'type' => $document->getClientOriginalExtension() === 'pdf' ? 'pdf' : 'image',
            'mime_type' => $document->getMimeType(),
            'disk' => config('filesystems.default'),
            'extension' => $document->getClientOriginalExtension(),
            'size' => $document->getSize(),
        ];

        if ($model->$relation) {
            $model->$relation()->update($data);
        } else {
            $model->$relation()->create($data);
        }

        Storage::putFileAs("$path/", $document, $fileName, 'public');
    }
}
