<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


use App\Models\Rental;
use App\Models\RentalProduct;

class RentalController extends Controller
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
                if($request->phone){
                    $rental = Rental::select('*')
                        ->with('product')
                        ->where('country_code',$request->country_code)
                        ->where('phone',$request->phone)
                        ->get();

                    $response = [
                        'success'=> true,
                        'message'=> 'List Rental By Phone',
                        'data'=> $rental
                    ];
                    return $response; 

                }else if($request->email){
                
                    // Rental List by email
                    $rental = Rental::select('*')
                        ->with('product')
                        ->where('country_code',$request->country_code)
                        ->where('email',$request->email)
                        ->get();


                    $response = [
                        'success'=> true,
                        'message'=> 'List Rental By Email',
                        'data'=> $rental
                    ];
                    return $response; 
                }else{
                    
                    // Rental List
                    $rental = Rental::select('*')
                    ->with('product')
                    ->where('country_code',$request->country_code)
                    ->get();

                    $response = [
                        'success'=> true,
                        'message'=> 'List Rental',
                        'data'=> $rental
                    ];
                    return $response; 
                }
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
        $token = $request->key;
        //dd($token);
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
                    'id_province'          => 'required',
                    'id_city'              => 'required',
                    
                ];

                if($request->country_code == 'id') {
                    $rules ['id_district']    = 'required';
                    $rules ['id_subdistrict'] = 'required';
                    $rules ['postal_code']    = 'required';
                }

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
                    'id_province'      => ($request->id_province) ? $request->id_province : null,
                    'id_city'          => ($request->id_city) ? $request->id_city : null,
                    'id_district'      => ($request->id_district) ? $request->id_district : null,
                    'id_village'       => ($request->id_subdistrict) ? $request->id_subdistrict : null,
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
