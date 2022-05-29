<?php

namespace App\Http\Controllers;

use App\Models\FormTradeIn;
use App\Models\FormTradeInProductInformation;
use App\Models\MasterCity;
use App\Models\MasterPostalCode;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class TradeInController extends Controller
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
                    $rental = FormTradeIn::select('*')
                        ->with('formTradeInProductInformation')
                        ->where('country_code',$request->country_code)
                        ->where('phone',$request->phone)
                        ->get();

                    $response = [
                        'success'=> true,
                        'message'=> 'List Trade In By Phone',
                        'data'=> $rental
                    ];
                    return $response; 

                }else if($request->email){
                
                    // Rental List by email
                    $rental = FormTradeIn::select('*')
                        ->with('formTradeInProductInformation')
                        ->where('country_code',$request->country_code)
                        ->where('email',$request->email)
                        ->get();


                    $response = [
                        'success'=> true,
                        'message'=> 'List Trade In By Email',
                        'data'=> $rental
                    ];
                    return $response; 
                }else{
                    
                    // Rental List
                    $rental = FormTradeIn::select('*')
                    ->with('formTradeInProductInformation')
                    ->with('formTradeInProductInformation.image')
                    ->where('country_code',$request->country_code)
                    ->get();

                    $response = [
                        'success'=> true,
                        'message'=> 'List Trade In',
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
            
        }else
        {
            $api_key = DB::table('api_clients')->select('*')->where('api_token', $token)->first();
            if (!$api_key){
                $response = [
                    'success'=> false,
                    'message'=> 'Token mismatch'
                ];
                return $response; 
            }else
            {
                $rules = [
                'title_code'  => 'required',
                'name'        => 'required|regex:/^[\pL\s\-]+$/u',
                'email'       => 'required|email',
                'phone'       => 'required|min:10|numeric',
                'address'     => 'required|min:8',
                // 'id_province' => 'required',
                // 'id_city'     => 'required',
                'declare'     => 'required',
                ];

                if($request->country_code == 'id') {
                // $rules ['id_district']    = 'required';
                // $rules ['id_subdistrict']     = 'required';
                // $rules ['id_postal_code'] = 'required';
                }

                foreach ($request->product_info as $key => $input) {
                    $rules["product_info.$key.*"]           = ['required'];
                    $rules["product_info.$key.brand"]       = ['required'];
                    $rules["product_info.$key.id_category"] = ['required'];
                    $rules["product_info.$key.age_product"] = ['required'];
                    $rules["product_info.$key.image"]       = ['required','file','image','max:250'];
                    // $rules["product_info.$key.requests.*"]  = ['required'];


                    $attributes["product_info.$key.brand"]       = "Brand";
                    $attributes["product_info.$key.id_category"] = "Category";            
                    $attributes["product_info.$key.age_product"] = "Age Product";            
                    $attributes["product_info.$key.image"]       = "Image";            
                    // $attributes["product_info.$key.requests.*"]  = "Request";
                }

                $messages = [
                    'required' => __('Please fill in this field'),
                    'email'    => "Please include an @ in the email address. '" . $request->email . "' is missing an '@'",
                    'regex'    => __('This field contain number')
                ];

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
                    $id_postal_code = $village->id;

                $attributes = [];

                $request->validate($rules, $messages, $attributes);
                $no_submission = 'TRD-'.substr($request->phone, -4).'-'.date('YmdHis');
                DB::beginTransaction();
                try {
                    $dataTrd = new FormTradeIn();

                    $insTrd  = [
                        'no_submission'    => $no_submission,
                        'csms_customer_id' => $request->csms_customer_id,
                        'csms_address_id'  => $request->csms_address_id,
                        'csms_phone_id'    => $request->csms_phone_id,
                        'country_code'     => $request->country_code,
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
                        'data_source'      => 'CHAT',
                    ];

                    $dataTrd->fill($insTrd)->save();

                    $idInsTrd = $dataTrd->id;

                    foreach ($request->product_info as $key => $info) {
                        $dataInfo = new FormTradeInProductInformation();

                        $insInfo = [
                            'id_form_trade_in' => $idInsTrd,
                            'brand'            => $info['brand'],
                            'id_category'      => $info['id_category'],
                            'age_product'      => $info['age_product'],
                            'requests'         => isset($info['requests']) ? $info['requests'] : [],
                        ];

                        $dataInfo->fill($insInfo)->save();

                        $idInsInfo = $dataInfo->id;

                        if ($request->hasFile("product_info.$key.image")) {
                            $this->storeFile(
                                $request->file("product_info.$key.image"),
                                $dataInfo, 
                                'image',
                                "images/form_trade_in/{$idInsTrd}/{$idInsInfo}",
                                'image'
                            );
                        }
                    }

                    $tradeInData = FormTradeIn::find($idInsTrd);

                    // $tradeInData = FormTradeIn::select('*')
                    //     ->with('formTradeInProductInformation')
                    //     ->with('formTradeInProductInformation.image')
                    //     ->where('id',$idInsTrd)
                    //     ->first();
                        
                        

                    // Mail::to(
                    //     env(
                    //         'CUSTOMERCARE_EMAIL_RECIPIENT',
                    //         'customercare@modena.com'
                    //     )
                    // )
                    // ->send(new \App\Mail\TradeInMail($tradeInData));

                    // if(env('CUSTOMERCARE_EMAIL_DEVMODE',false) == true) {
                    //     $bccs = explode(',', env('CUSTOMERCARE_EMAIL_BCC','dwiki.herdiansyah@modena.com'));

                    //     foreach ($bccs as $bcc) {
                    //         Mail::bcc($bcc)->send(new \App\Mail\TradeInMail($tradeInData));
                    //     }
                    // }

                    DB::commit();

                    $response = [
                        'success'=> true,
                        'message'=> 'Your information has been saved, please wait for a reply from our customer service !'
                    ];
                    return $response; 

                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                    $response = [
                        'success'=> false,
                        'message'=> 'Form Submit failed !'
                    ];
                    return $response;
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
            'name'         => $document->getClientOriginalName(),
            'path'         => $path,
            'file_name'    => $fileName,
            'type'         => $document->getClientOriginalExtension() === 'pdf' ? 'pdf' : 'image',
            'mime_type'    => $document->getMimeType(),
            'disk'         => config('filesystems.default'),
            'extension'    => $document->getClientOriginalExtension(),
            'size'         => $document->getSize(),
        ];

        if ($model->$relation) {
            $model->$relation()->update($data);
        } else {
            $model->$relation()->create($data);
        }

        Storage::putFileAs("$path/", $document, $fileName, 'public');
    }
}
