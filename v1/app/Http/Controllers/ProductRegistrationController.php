<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use File;

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
                $request->validate([
                    'first_name' => ['required'],
                    'last_name' => ['required'],
                    'address' => ['required'],
                    'email' => ['required', 'email'],
                    'phone' => ['required','min:8'],
                    'brand' => ['required'],
                    'product_name' => ['required'],
                    'serial_number' => ['required'],
                    'purchase_date' => ['required', 'date'],
                    'store_name' => ['required'],
                    'invoice' => ['required'],
                    'warranty' => ['required'],
                ]);

                DB::beginTransaction();
                try {
                    $village = MasterPostalCode::select('*')
						->where('postal_code', $request->postal_code)
						->first();

                    $district = DB::table('master_village')
                    	->select(
							'id_village',
							'id_district',
                    		DB::raw("(SELECT DISTINCT(id_city) FROM master_district WHERE id_district=master_village.id_district) as id_city")
                    	)
                    	->where('id_village', $village->id_village)
                    	->first();

                    $province = MasterCity::select('*')
						->where('id_city', $district->id_city)
						->first();

					$village_id     = $village->id_village;
					$district_id    = $district->id_district;
					$city_id        = $district->id_city;
					$province_id    = $province->id_province;

                    $data = new ProductRegistration();
                    $insReg  = [
                        'user_id' => $request->user_id,
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'address' => $request->address,
                        'province_id' => $province_id,
                        'city_id' => $city_id,
						'district_id' => $district_id,
						'subdistrict_id' => $village_id,
						'postal_code' => $request->postal_code,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'brand' => $request->brand,
                        'product_name' => $request->product_name,
                        'serial_number' => $request->serial_number,
                        'purchase_date' => $request->purchase_date,
                        'store_name' => $request->store_name,
                        'country_code' => $request->country_code,
                        'data_source' => 'CHAT',
                    ];

                    //$data->user_id = Auth::id();
                    $data->fill($insReg);
                    $data->save();
                    if ($request->invoice) {
						$url = $request->invoice;
						$fileName = substr($url, strrpos($url, '/') + 1);
						$image = file_get_contents($request->invoice);

						file_put_contents(storage_path('temp/'.$fileName), $image);

						$file = File::get(storage_path('temp/'.$fileName));

                        if (!empty($image)) {
                            $this->storeFile($image, $data, 'invoice', "invoices/{$data->id}", 'invoice', storage_path('temp/'.$fileName));

							unlink(storage_path('temp/'.$fileName));
                        }
                    }
                    if ($request->warranty) {
						$url = $request->warranty;
						$fileName = substr($url, strrpos($url, '/') + 1);
						$image = file_get_contents($request->warranty);

						file_put_contents(storage_path('temp/'.$fileName), $image);

						$file = File::get(storage_path('temp/'.$fileName));

                        if (!empty($image)) {
                            $this->storeFile($image, $data, 'warranty', "warranties/{$data->id}", 'warranty', storage_path('temp/'.$fileName));

							unlink(storage_path('temp/'.$fileName));
                        }
                    }
                    DB::commit();
                    $response = [
                        'success'=> true,
                        'message'=> 'Your information has been saved, please wait for a reply from our customer service'

                    ];

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

		return $response;
    }

	protected function storeFile($file, $model, $relation, $path, $content_type = null, $fileTempSource)
    {
        $document = $file;
		$mimeType = File::mimeType($fileTempSource);
		$extension = File::extension($fileTempSource);
		$size = File::size($fileTempSource);
		$name = File::name($fileTempSource);

        $data = [
            'content_type' => $content_type,
            'name'         => $name.'.'.$extension,
            'path'         => $path,
            'file_name'    => $name.'.'.$extension,
            'type'         => 'image',
            'mime_type'    => $mimeType,
            'disk'         => config('filesystems.default'),
            'extension'    => $extension,
            'size'         => $size,
        ];

        if ($model->$relation) {
            $model->$relation()->update($data);
        } else {
            $model->$relation()->create($data);
        }

        Storage::disk('sftp')->put("$path/".$name.'.'.$extension, $document);
    }
}
