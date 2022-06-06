<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use App\Models\Rental;
use App\Models\RentalProduct;
use App\Models\MasterCity;
use App\Models\MasterPostalCode;

class RentalController extends Controller
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
				if(!empty($request->phone)){
					if($request->email){
						$rental = Rental::select('*')
							->with('product')
							->where('country_code', $request->country_code)
							->where('phone', $request->phone)
							->Orwhere('email', $request->email)
							->get();

						$response = [
							'success'=> true,
							'message'=> 'List Rental By Phone',
							'data'=> $rental
						];
					}else{
						$rental = Rental::select('*')
							->with('product')
							->where('country_code', $request->country_code)
							->where('phone', $request->phone)
							->get();

						$response = [
							'success'=> true,
							'message'=> 'List Rental By Phone',
							'data'=> $rental
						];
					}
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
            return $response;

        }else{
            $api_key = DB::table('api_clients')->select('*')->where('api_token', $token)->first();

            if (!$api_key){
                $response = [
                    'success'=> false,
                    'message'=> 'Token mismatch'
                ];
                return $response;
            }else{
                $rules      = [
                    'csms_customer_id'     => 'required',
                    'csms_address_id'      => 'required',
                    'csms_phone_id'        => 'required',
                    'email'                => 'required|email',
                    'phone'                => 'required|min:10|numeric',
                    'title_code'           => 'required',
                    'name'                 => 'required|regex:/^[\pL\s\-]+$/u',
                    'address'              => 'required',
                    // 'id_province'          => 'required',
                    // 'id_city'              => 'required',

                ];

                if($request->country_code == 'id') {
                    // $rules ['id_district']    = 'required';
                    // $rules ['id_subdistrict'] = 'required';
                    $rules ['postal_code']    = 'required';
                }

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


                $messages = [
                    'required' => __('Please fill in this field'),
                    'email'    => "Please include an @ in the email address. '" . $request->email . "' is missing an '@'",
                    'regex'    => __('This field contain number')
                ];

                $attributes = [];

                $request->validate($rules, $messages, $attributes);

                $no_submission = 'RENT-'.substr($request->phone, -4).'-'.date('YmdHis');

                $dataRent = new Rental();

                $insRent  = [
                    'no_submission'    => $no_submission,
                    'csms_customer_id' => $request->csms_customer_id,
                    'csms_address_id'  => $request->csms_address_id,
                    'csms_phone_id'    => $request->csms_phone_id,
                    'country_code'     => $request->country_code,
                    // 'id_province'      => ($request->id_province) ? $request->id_province : null,
                    // 'id_city'          => ($request->id_city) ? $request->id_city : null,
                    // 'id_district'      => ($request->id_district) ? $request->id_district : null,
                    // 'id_village'       => ($request->id_subdistrict) ? $request->id_subdistrict : null,

                    'id_province'      => ($id_province) ? $id_province : null,
                    'id_city'          => ($id_city) ? $id_city : null,
                    'id_district'      => ($id_district) ? $id_district : null,
                    'id_village'       => ($id_village) ? $id_village : null,

                    'postal_code'      => ($request->postal_code) ? $request->postal_code : null,
                    'title_code'       => $request->title_code,
                    'name'             => $request->name,
                    'phone'            => $request->phone,
                    'email'            => $request->email,
                    'address'          => $request->address,
                    'data_source'          => 'CHAT',

                ];

                $dataRent->fill($insRent)->save();
                $idInsRent = $dataRent->id;

                foreach ($request->product_detail as $key => $product) {
                    $dataProduct = new RentalProduct();

                    $insProduct = [
                        'id_form_rental'       => $idInsRent,
                        'id_product_master_id' => $product['id_product_master_id'],
                        'name'                 => $product['name'],
                        'qty'                  => $product['qty']
                    ];

                    $dataProduct->fill($insProduct)->save();

                    $idInsProduct = $dataProduct->id;
                }

                $rentalData = Rental::find($idInsRent);

                // Mail::to(
                //     env(
                //         'CUSTOMERCARE_EMAIL_RECIPIENT',
                //         'customercare@modena.com'
                //     )
                // )
                // ->send(new \App\Mail\RentalMail($rentalData));

                // if(env('CUSTOMERCARE_EMAIL_DEVMODE',false) == true) {
                //     $bccs = explode(',', env('CUSTOMERCARE_EMAIL_BCC','dwiki.herdiansyah@modena.com'));

                //     foreach ($bccs as $bcc) {
                //         Mail::bcc($bcc)->send(new \App\Mail\RentalMail($rentalData));
                //     }
                // }


                $response = [
                    'success'=> true,
                    'message'=> 'Your rental request form has been submitted'
                ];

                return $response;
                 }
            }
    }
}
