<?php

namespace App\Http\Controllers;
use App\Model\ServiceCenter;
use App\Model\ServiceCenterId;
#Models
use App\Model\FormService;
use App\Model\FormServiceCenter;
use App\Model\FormContractService;
use App\Model\FormTradeIn;
use App\Model\FormTradeInProductInformation;
use App\Model\FormRental;
use App\Model\FormRentalProduct;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceCenterController extends Controller
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
                'message'=> 'List Service'
            ];
            return $response;
           
        }else{
            $api_key = DB::table('api_clients')->select('*')->where('api_token', $token)->first();

            if (!$api_key){
                $response = [
                    'success'=> false,
                    'message'=> 'List Service'
                ];
                return $response; 
            }else{
                // Service List
                if(!$request->email)
                {
                    $transaction = DB::table('form_service_center')->select('*')
                    ->where('country_code',$request->country_code)
                    ->get();
                    $response = [
                        'success'=> true,
                        'message'=> 'List Service',
                        'data'=> $transaction
                    ];
                    return $response;
                }else{
                    if(!$request->product_id){
                        $transaction = DB::table('form_service_center')->select('*')
                        ->where('country_code',$request->country_code)
                        ->where('email',$request->email)
                        ->get();
                        $response = [
                            'success'=> true,
                            'message'=> 'List Service By Email',
                            'data'=> $transaction
                        ];
                        return $response;
                    }else{
                        if(!$request->ron_number){
                            $transaction = DB::table('form_service_center')->select('*')
                            ->where('country_code',$request->country_code)
                            ->where('email',$request->email)
                            ->where('product_id',$request->product_id)
                            ->get();
                            $response = [
                                'success'=> true,
                                'message'=> 'List Service By Product Id',
                                'data'=> $transaction
                            ];
                            return $response;
                        }else{
                            $transaction = DB::table('form_service_center')->select('*')
                            ->where('country_code',$request->country_code)
                            ->where('email',$request->email)
                            ->where('csms_ron_number',$request->csms_ron_number)
                            ->get();
                            $response = [
                                'success'=> true,
                                'message'=> 'List Service By RON Number',
                                'data'=> $transaction
                            ];
                            return $response;
                        }
                    }
                }


            }
        }
        
    }

    public function store(Request $request)
    {
        $url          = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode(env('NOCAPTCHA_SECRET')) .  '&response=' . urlencode($request->input('g-recaptcha-response'));
        $response     = file_get_contents($url);
        $responseKeys = json_decode($response,true);

        if (
            !in_array(
                $request->category, 
                [
                    'Contract Service',
                    'Service Center',
                    'Trade In',
                    'Rental',
                ]
            )
        ) {
            if(!$responseKeys['success']){
                session()->flash('error-message', langLocale('notifications.please_check_captcha_box'));
                return redirect()->back()->withInput();
            }
        }

        if ($request->category == 'Contract Service') {
            $rules = [
                'title_code'    => 'required',
                'name'          => 'required|regex:/^[\pL\s\-]+$/u',
                'email'         => 'required|email',
                'phone'         => 'required|min:10|numeric',
                'address'       => 'required|min:8',
                'province'      => 'required',
                'city'          => 'required',
                'brand'         => 'required',
                'id_category'   => 'required',
                'product_model' => 'required',
                'age_product'   => 'required',
                // 'request.*'     => 'required',
            ];

            if(getLocaleCountryCode() == 'id') {
               $rules ['district']    = 'required';
               $rules ['subdistrict'] = 'required';
               $rules ['postal_code']    = 'required';
            }

            $messages = [
                'required' => __('Please fill in this field'),
                'email'    => "Please include an @ in the email address. '" . $request->email . "' is missing an '@'",
                'regex'    => __('This field contain number')
            ];

            $attributes = [];

            $request->validate($rules, $messages, $attributes);

            if(!$responseKeys['success']){
                session()->flash('error-message', langLocale('notifications.please_check_captcha_box'));
                return redirect()->back()->withInput();
            }

            if (getLocaleCountryCode() == 'id') {
                $checkDataKonsumen = $this->checkDataKonsumentCsms($request->phone);

                if (count($checkDataKonsumen) == count($checkDataKonsumen, COUNT_RECURSIVE)) {
                    $createDataKonsumen = $this->simpanDataKonsumenCsms([
                                            'title_code'           => $request->title_code,
                                            'customer_name'        => $request->name,
                                            'customer_phone_no'    => $request->phone,
                                            'customer_email'       => $request->email,
                                            'customer_address'     => $request->address,  
                                            'customer_province'    => $request->province,  
                                            'customer_city'        => $request->city,  
                                            'customer_district'    => $request->district,  
                                            'customer_village'     => $request->subdistrict,  
                                            'customer_postal_code' => $request->postal_code,  
                                        ]);

                    $csms_customer_id = (!empty($createDataKonsumen)) ? $createDataKonsumen['customer_id'] : '';
                    $csms_address_id  = (!empty($createDataKonsumen)) ? $createDataKonsumen['address_id'] : '';
                    $csms_phone_id    = (!empty($createDataKonsumen)) ? $createDataKonsumen['phone_id'] : '';
                } else {
                    $createDataKonsumen = $this->simpanDataKonsumenCsms([
                                            'customer_id'          => $checkDataKonsumen[0]['customer_id'],
                                            'title_code'           => $request->title_code,
                                            'customer_name'        => $request->name,
                                            'customer_phone_no'    => $request->phone,
                                            'customer_email'       => $request->email,
                                            'customer_address'     => $request->address,  
                                            'customer_province'    => $request->province,  
                                            'customer_city'        => $request->city,  
                                            'customer_district'    => $request->district,  
                                            'customer_village'     => $request->subdistrict,  
                                            'customer_postal_code' => $request->postal_code,
                                        ]);

                    $csms_customer_id = (!empty($createDataKonsumen)) 
                                      ? $createDataKonsumen['customer_id'] 
                                      : $checkDataKonsumen[0]['customer_id'];

                    $csms_address_id  = (!empty($createDataKonsumen)) 
                                      ? $createDataKonsumen['address_id'] 
                                      : $checkDataKonsumen[0]['alamat_id'];

                    $csms_phone_id    = (!empty($createDataKonsumen)) 
                                      ? $createDataKonsumen['phone_id'] 
                                      : $checkDataKonsumen[0]['phone_id'];
                }
                /*$csms_customer_id = 'CUST-ID-'.$request->phone;
                $csms_address_id  = 'CUST-ADDR-'.$request->phone;
                $csms_phone_id    = 'CUST-PHONE-'.$request->phone;*/
            } else {
                $csms_customer_id = 'CUST-ID-'.$request->phone;
                $csms_address_id  = 'CUST-ADDR-'.$request->phone;
                $csms_phone_id    = 'CUST-PHONE-'.$request->phone;
            }
            
            $no_submission = 'RCS-'.substr($request->phone, -4).'-'.date('YmdHis');

            DB::beginTransaction();
            try {
                $data = new FormContractService();

                $ins  = [
                    'no_submission'    => $no_submission,
                    'csms_customer_id' => $csms_customer_id,
                    'csms_address_id'  => $csms_address_id,
                    'csms_phone_id'    => $csms_phone_id,
                    'country_code'     => getLocaleCountryCode(),
                    'id_province'      => ($request->id_province) ? $request->id_province : null,
                    'id_city'          => ($request->id_city) ? $request->id_city : null,
                    // 'id_district'      => ($request->district_id) ? $request->district_id : null,
                    // 'id_village'       => ($request->subdistrict_id) ? $request->subdistrict_id : null,
                    // 'postal_code'      => ($request->postal_code) ? $request->postal_code : null,
                    'name'             => $request->name,
                    'phone'            => $request->phone,
                    'email'            => $request->email,
                    'address'          => $request->address,
                    'id_category'      => $request->id_category,
                    'brand'            => $request->brand,
                    'product_model'    => $request->product_model,
                    'age_product'      => $request->age_product,
                    'requests'         => isset($request->requests) ? $request->requests : [],
                ];

                $data->fill($ins)->save();

                $idIns = $data->id;

                if ($request->hasFile('image')) {
                    $this->storeFile(
                        $request->file('image'),
                        $data, 
                        'image',
                        "images/form_contract_service/{$idIns}",
                        'image'
                    );
                }

                DB::commit();
                
                session()->flash('success-message', trans('Your information has been saved, please wait for a reply from our customer service'));
                return redirect(routeLocale('service-contract.index'));

            } catch (\Exception $e) {

                DB::rollback();
                throw $e;
                session()->flash('error-message', trans('Form submit failed'));
                return redirect()->back()->withInput();
            }
        } elseif ($request->category == 'Service Center') {
            $rules = [
                'email'              => 'required|email',
                'phone'              => 'required|min:10|numeric',
                'title_code'         => 'required',
                'first_name'         => 'required|regex:/^[\pL\s\-]+$/u',
                'last_name'          => 'required|regex:/^[\pL\s\-]+$/u',
                'address'            => 'required|min:8',
                'id_province'        => 'required',
                'province'           => 'required',
                'id_city'            => 'required',
                'city'               => 'required',
                'product_identifier' => 'required',
                'product_name'       => 'required',
                'series_number'      => 'required_if:product_identifier,==,SN',
                'product_id'         => 'required',
                'id_problem'         => 'required',
                'problem'            => 'required',
                'date'               => 'required',
                // 'time'            => 'required',
                // 'image'           => 'required|image',
                // 'invoice'         => 'mimes:jpg,jpeg,png,svg,doc,docx,pdf'
            ];

            if(getLocaleCountryCode() == 'id') {
               $rules ['district_id']    = 'required';
               $rules ['district']       = 'required';
               $rules ['subdistrict_id'] = 'required';
               $rules ['subdistrict']    = 'required';
               $rules ['postal_code_id'] = 'required';
               $rules ['postal_code']    = 'required';
            }

            $messages = [
                'required' => __('Please fill in this field'),
                'email'    => "Please include an @ in the email address. '" . $request->email . "' is missing an '@'",
                'regex'    => __('This field contain number')
            ];

            $attributes = [];

            $request->validate($rules, $messages, $attributes);

            if(!$responseKeys['success']){
                session()->flash('error-message', langLocale('notifications.please_check_captcha_box'));
                return redirect()->back()->withInput();
            }

            if (getLocaleCountryCode() == 'id') {
                $checkDataKonsumen = $this->checkDataKonsumentCsms($request->phone);

                if (count($checkDataKonsumen) == count($checkDataKonsumen, COUNT_RECURSIVE)) {
                    $createDataKonsumen = $this->simpanDataKonsumenCsms([
                                            'title_code'           => $request->title_code,
                                            'customer_name'        => $request->first_name.' '.$request->last_name,
                                            'customer_phone_no'    => $request->phone,
                                            'customer_email'       => $request->email,
                                            'customer_address'     => $request->address,  
                                            'customer_province'    => $request->province,  
                                            'customer_city'        => $request->city,  
                                            'customer_district'    => $request->district,  
                                            'customer_village'     => $request->subdistrict,  
                                            'customer_postal_code' => $request->postal_code,  
                                        ]);

                    $csms_customer_id = (!empty($createDataKonsumen)) ? $createDataKonsumen['customer_id'] : '';
                    $csms_address_id  = (!empty($createDataKonsumen)) ? $createDataKonsumen['address_id'] : '';
                    $csms_phone_id    = (!empty($createDataKonsumen)) ? $createDataKonsumen['phone_id'] : '';
                } else {
                    $createDataKonsumen = $this->simpanDataKonsumenCsms([
                                            'customer_id'          => $checkDataKonsumen[0]['customer_id'],
                                            'customer_address_id'  => $checkDataKonsumen[0]['alamat_id'],
                                            'title_code'           => $request->title_code,
                                            'customer_name'        => $request->first_name.' '.$request->last_name,
                                            'customer_phone_no'    => $request->phone,
                                            'customer_email'       => $request->email,
                                            'customer_address'     => $request->address,  
                                            'customer_province'    => $request->province,  
                                            'customer_city'        => $request->city,  
                                            'customer_district'    => $request->district,  
                                            'customer_village'     => $request->subdistrict,  
                                            'customer_postal_code' => $request->postal_code,  
                                        ]);

                    $csms_customer_id = (!empty($createDataKonsumen)) 
                                      ? $createDataKonsumen['customer_id'] 
                                      : $checkDataKonsumen[0]['customer_id'];

                    $csms_address_id  = (!empty($createDataKonsumen)) 
                                      ? $createDataKonsumen['address_id'] 
                                      : $checkDataKonsumen[0]['alamat_id'];

                    $csms_phone_id    = (!empty($createDataKonsumen)) 
                                      ? $createDataKonsumen['phone_id'] 
                                      : $checkDataKonsumen[0]['phone_id'];
                }

                $ronParams = [
                    'customer_id'   => $csms_customer_id,
                    'address_id'    => $csms_address_id,
                    'phone_id'      => $csms_phone_id,
                    'product_id'    => $request->product_id,
                    'keluhan_id'    => $request->id_problem,
                    'keluhan_desc'  => $request->problem,
                    'schedule_date' => date('d/m/Y',strtotime($request->date))
                ];

                if(!empty($request->series_number)) {
                    $ronParams['serial_number'] = $request->series_number;
                }

                $createRon  = $this->simpanRonCsms($ronParams);

                $ron_number = !empty($createRon) ? $createRon['ron_number'] : '';

                /*$csms_customer_id = 'CUST-ID-'.$request->phone;
                $csms_address_id  = 'CUST-ADDR-'.$request->phone;
                $csms_phone_id    = 'CUST-PHONE-'.$request->phone;
                $ron_number       = 'REQ-'.substr($request->phone, -4).'-'.date('YmdHis');*/
            } else {
                $csms_customer_id = 'CUST-ID-'.$request->phone;
                $csms_address_id  = 'CUST-ADDR-'.$request->phone;
                $csms_phone_id    = 'CUST-PHONE-'.$request->phone;
                $ron_number       = 'REQ-'.substr($request->phone, -4).'-'.date('YmdHis');
            }

            DB::beginTransaction();
            try {
                $data = new FormServiceCenter();

                $ins = [
                    'csms_ron_number'    => $ron_number,
                    'csms_customer_id'   => $csms_customer_id,
                    'csms_address_id'    => $csms_address_id,
                    'csms_phone_id'      => $csms_phone_id,
                    'csms_problem_id'    => $request->id_problem,
                    'csms_problem_desc'  => $request->problem,
                    'product_identifier' => $request->product_identifier,
                    'product_id'         => $request->product_id,
                    'serial_number'      => ($request->series_number) ? $request->series_number : null,
                    'product_name'      => ($request->product_name) ? $request->product_name : null,
                    'country_code'       => getLocaleCountryCode(),
                    'id_province'        => ($request->id_province) ? $request->id_province : null,
                    'id_city'            => ($request->id_city) ? $request->id_city : null,
                    'id_district'        => ($request->district_id) ? $request->district_id : null,
                    'id_village'         => ($request->subdistrict_id) ? $request->subdistrict_id : null,
                    'postal_code'        => ($request->postal_code) ? $request->postal_code : null,
                    'phone'              => $request->phone,
                    'email'              => $request->email,
                    'first_name'         => $request->first_name,
                    'last_name'          => $request->last_name,
                    'address'            => $request->address,
                    'date'               => $request->date,
                ];

                $data->fill($ins)->save();

                // upload file
                /*$this->storeFile($request->file('image'), $data, 'image', "images/services/{$data->id}", 'image');

                if ($request->file('invoice')) {
                    $this->storeFile($request->file('invoice'), $data, 'invoice', "images/services/{$data->id}", 'invoice');
                }*/

                DB::commit();
                
                session()->flash('success-message', trans('Your information has been saved, please wait for a reply from our customer service'));

                return redirect(routeLocale('service-center.index'));
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
                session()->flash('error-message', trans('Form submit failed'));
                return redirect()->back()->withInput();
            }
        } elseif ($request->category == 'Trade In') {
            Session::flash('count-form-info',count($request->product_info));

            $rules = [
                'title_code'  => 'required',
                'name'        => 'required|regex:/^[\pL\s\-]+$/u',
                'email'       => 'required|email',
                'phone'       => 'required|min:10|numeric',
                'address'     => 'required|min:8',
                'id_province' => 'required',
                'id_city'     => 'required',
                'declare'     => 'required',
            ];

            if(getLocaleCountryCode() == 'id') {
               $rules ['id_district']    = 'required';
               $rules ['id_village']     = 'required';
               $rules ['id_postal_code'] = 'required';
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

            $attributes = [];

            $request->validate($rules, $messages, $attributes);

            if(!$responseKeys['success']){
                session()->flash('error-message', langLocale('notifications.please_check_captcha_box'));
                return redirect()->back()->withInput();
            }

            if (getLocaleCountryCode() == 'id') {
                $checkDataKonsumen = $this->checkDataKonsumentCsms($request->phone);

                if (count($checkDataKonsumen) == count($checkDataKonsumen, COUNT_RECURSIVE)) {
                    $createDataKonsumen = $this->simpanDataKonsumenCsms([
                                            'title_code'           => $request->title_code,
                                            'customer_name'        => $request->name,
                                            'customer_phone_no'    => $request->phone,
                                            'customer_email'       => $request->email,
                                            'customer_address'     => $request->address,  
                                            'customer_province'    => $request->province,  
                                            'customer_city'        => $request->city,  
                                            'customer_district'    => $request->district,  
                                            'customer_village'     => $request->subdistrict,  
                                            'customer_postal_code' => $request->postal_code,  
                                        ]);

                    $csms_customer_id = (!empty($createDataKonsumen)) ? $createDataKonsumen['customer_id'] : '';
                    $csms_address_id  = (!empty($createDataKonsumen)) ? $createDataKonsumen['address_id'] : '';
                    $csms_phone_id    = (!empty($createDataKonsumen)) ? $createDataKonsumen['phone_id'] : '';
                } else {
                    $createDataKonsumen = $this->simpanDataKonsumenCsms([
                                            'customer_id'          => $checkDataKonsumen[0]['customer_id'],
                                            'customer_address_id'  => $checkDataKonsumen[0]['alamat_id'],
                                            'title_code'           => $request->title_code,
                                            'customer_name'        => $request->name,
                                            'customer_phone_no'    => $request->phone,
                                            'customer_email'       => $request->email,
                                            'customer_address'     => $request->address,  
                                            'customer_province'    => $request->province,  
                                            'customer_city'        => $request->city,  
                                            'customer_district'    => $request->district,  
                                            'customer_village'     => $request->subdistrict,  
                                            'customer_postal_code' => $request->postal_code,
                                        ]);

                    $csms_customer_id = (!empty($createDataKonsumen)) 
                                      ? $createDataKonsumen['customer_id'] 
                                      : $checkDataKonsumen[0]['customer_id'];

                    $csms_address_id  = (!empty($createDataKonsumen)) 
                                      ? $createDataKonsumen['address_id'] 
                                      : $checkDataKonsumen[0]['alamat_id'];

                    $csms_phone_id    = (!empty($createDataKonsumen)) 
                                      ? $createDataKonsumen['phone_id'] 
                                      : $checkDataKonsumen[0]['phone_id'];

                    /*$csms_customer_id = $checkDataKonsumen[0]['customer_id'];
                    $csms_address_id  = $checkDataKonsumen[0]['alamat_id'];
                    $csms_phone_id    = $checkDataKonsumen[0]['phone_id'];*/
                }
                /*$csms_customer_id = 'CUST-ID-'.$request->phone;
                $csms_address_id  = 'CUST-ADDR-'.$request->phone;
                $csms_phone_id    = 'CUST-PHONE-'.$request->phone;*/
            } else {
                $csms_customer_id = 'CUST-ID-'.$request->phone;
                $csms_address_id  = 'CUST-ADDR-'.$request->phone;
                $csms_phone_id    = 'CUST-PHONE-'.$request->phone;
            }
            
            $no_submission = 'TRD-'.substr($request->phone, -4).'-'.date('YmdHis');

            DB::beginTransaction();
            try {
                $dataTrd = new FormTradeIn();

                $insTrd  = [
                    'no_submission'    => $no_submission,
                    'csms_customer_id' => $csms_customer_id,
                    'csms_address_id'  => $csms_address_id,
                    'csms_phone_id'    => $csms_phone_id,
                    'country_code'     => getLocaleCountryCode(),
                    'id_province'      => ($request->id_province) ? $request->id_province : null,
                    'id_city'          => ($request->id_city) ? $request->id_city : null,
                    'id_district'      => ($request->id_district) ? $request->id_district : null,
                    'id_village'       => ($request->id_village) ? $request->id_village : null,
                    'postal_code'      => ($request->postal_code) ? $request->postal_code : null,
                    'name'             => $request->name,
                    'phone'            => $request->phone,
                    'email'            => $request->email,
                    'address'          => $request->address,
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

                Mail::to(
                    env(
                        'CUSTOMERCARE_EMAIL_RECIPIENT',
                        'customercare@modena.com'
                    )
                )
                ->send(new \App\Mail\TradeInMail($tradeInData));

                if(env('CUSTOMERCARE_EMAIL_DEVMODE',false) == true) {
                    $bccs = explode(',', env('CUSTOMERCARE_EMAIL_BCC','dwiki.herdiansyah@modena.com'));

                    foreach ($bccs as $bcc) {
                        Mail::bcc($bcc)->send(new \App\Mail\TradeInMail($tradeInData));
                    }
                }

                DB::commit();
                
                session()->flash('success-message', trans('Your information has been saved, please wait for a reply from our customer service'));
                return redirect(routeLocale('service-trade-in.index'));
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
                session()->flash('error-message', trans('Form submit failed'));
                return redirect()->back()->withInput();
            }
        } elseif ($request->category == 'Rental') {
            $rules      = [
                'title_code'           => 'required',
                'name'                 => 'required',
                'email'                => 'required',
                'phone'                => 'required',
                'address'              => 'required',
                'id_province'          => 'required',
                // 'province'             => 'required',
                'id_city'              => 'required',
                // 'city'                 => 'required',
                'id_district'          => 'required',
                // 'district'             => 'required',
                'id_village'           => 'required',
                // 'subdistrict'          => 'required',
                'id_postal_code'       => 'required',
                'postal_code'          => 'required',
                // 'g-recaptcha-response' => 'required',
            ];

            $messages   = [
                // 'required'             => __('Check Your Security Code'),
                // 'g-recaptcha-response' => __('Check Your Security Code')
            ];

            $attributes = [];

            foreach ($request->product as $key => $product) {
                $rules["product.$key.*"]                    = ['required'];
                $rules["product.$key.id_product_master_id"] = ['required'];
                // $rules["product.$key.name"]                 = ['required'];
                $rules["product.$key.qty"]                  = ['required','min:1'];

                $attributes["product.$key.id_product_master_id"] = "Product";
                // $attributes["product.$key.name"]                 = "Product Name";
                $attributes["product.$key.qty"]                  = "Quantity";
            }

            $request->validate($rules, $messages, $attributes);

            if(!$responseKeys['success']){
                session()->flash('error-message', langLocale('notifications.please_check_captcha_box'));
                return redirect()->back()->withInput();
            }

            if (getLocaleCountryCode() == 'id') {
                $checkDataKonsumen = $this->checkDataKonsumentCsms($request->phone);

                if (count($checkDataKonsumen) == count($checkDataKonsumen, COUNT_RECURSIVE)) {
                    $createDataKonsumen = $this->simpanDataKonsumenCsms([
                                            'title_code'           => $request->title_code,
                                            'customer_name'        => $request->name,
                                            'customer_phone_no'    => $request->phone,
                                            'customer_email'       => $request->email,
                                            'customer_address'     => $request->address,  
                                            'customer_province'    => $request->province,  
                                            'customer_city'        => $request->city,  
                                            'customer_district'    => $request->district,  
                                            'customer_village'     => $request->subdistrict,  
                                            'customer_postal_code' => $request->postal_code,  
                                        ]);

                    $csms_customer_id = (!empty($createDataKonsumen)) ? $createDataKonsumen['customer_id'] : '';
                    $csms_address_id  = (!empty($createDataKonsumen)) ? $createDataKonsumen['address_id'] : '';
                    $csms_phone_id    = (!empty($createDataKonsumen)) ? $createDataKonsumen['phone_id'] : '';
                } else {
                    $createDataKonsumen = $this->simpanDataKonsumenCsms([
                                            'customer_id'          => $checkDataKonsumen[0]['customer_id'],
                                            'customer_address_id'  => $checkDataKonsumen[0]['alamat_id'],
                                            'title_code'           => $request->title_code,
                                            'customer_name'        => $request->name,
                                            'customer_phone_no'    => $request->phone,
                                            'customer_email'       => $request->email,
                                            'customer_address'     => $request->address,  
                                            'customer_province'    => $request->province,  
                                            'customer_city'        => $request->city,  
                                            'customer_district'    => $request->district,  
                                            'customer_village'     => $request->subdistrict,  
                                            'customer_postal_code' => $request->postal_code,
                                        ]);

                    $csms_customer_id = (!empty($createDataKonsumen)) 
                                      ? $createDataKonsumen['customer_id'] 
                                      : $checkDataKonsumen[0]['customer_id'];

                    $csms_address_id  = (!empty($createDataKonsumen)) 
                                      ? $createDataKonsumen['address_id'] 
                                      : $checkDataKonsumen[0]['alamat_id'];

                    $csms_phone_id    = (!empty($createDataKonsumen)) 
                                      ? $createDataKonsumen['phone_id'] 
                                      : $checkDataKonsumen[0]['phone_id'];
                }
            } else {
                $csms_customer_id = 'CUST-ID-'.$request->phone;
                $csms_address_id  = 'CUST-ADDR-'.$request->phone;
                $csms_phone_id    = 'CUST-PHONE-'.$request->phone;
            }
            
            $no_submission = 'RENT-'.substr($request->phone, -4).'-'.date('YmdHis');

            DB::beginTransaction();
            try {
                $dataRent = new FormRental();

                $insRent  = [
                    'no_submission'    => $no_submission,
                    'csms_customer_id' => $csms_customer_id,
                    'csms_address_id'  => $csms_address_id,
                    'csms_phone_id'    => $csms_phone_id,
                    'country_code'     => getLocaleCountryCode(),
                    'id_province'      => ($request->id_province) ? $request->id_province : null,
                    'id_city'          => ($request->id_city) ? $request->id_city : null,
                    'id_district'      => ($request->id_district) ? $request->id_district : null,
                    'id_village'       => ($request->id_village) ? $request->id_village : null,
                    'postal_code'      => ($request->postal_code) ? $request->postal_code : null,
                    'name'             => $request->name,
                    'phone'            => $request->phone,
                    'email'            => $request->email,
                    'address'          => $request->address,
                ];

                $dataRent->fill($insRent)->save();

                $idInsRent = $dataRent->id;

                foreach ($request->product as $key => $product) {
                    $dataProduct = new FormRentalProduct();

                    $insProduct = [
                        'id_form_rental'       => $idInsRent,
                        'id_product_master_id' => $product['id_product_master_id'],
                        'name'                 => $product['name'],
                        'qty'                  => $product['qty']
                    ];

                    $dataProduct->fill($insProduct)->save();

                    $idInsProduct = $dataProduct->id;
                }

                $rentalData = FormRental::find($idInsRent);

                Mail::to(
                    env(
                        'CUSTOMERCARE_EMAIL_RECIPIENT',
                        'customercare@modena.com'
                    )
                )
                ->send(new \App\Mail\RentalMail($rentalData));

                if(env('CUSTOMERCARE_EMAIL_DEVMODE',false) == true) {
                    $bccs = explode(',', env('CUSTOMERCARE_EMAIL_BCC','dwiki.herdiansyah@modena.com'));

                    foreach ($bccs as $bcc) {
                        Mail::bcc($bcc)->send(new \App\Mail\RentalMail($rentalData));
                    }
                }

                DB::commit();

                session()->flash('success-message', trans('Your rental request form has been submitted'));
                return redirect()->intended(routeLocale('service-rental.index')); 
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
                session()->flash('error-message', trans('Form submit failed'));
                return redirect()->back()->withInput();
            }
        } elseif ($request->category == 'Keep In Touch') {
            $request->validate([
                'first_name' => 'required|regex:/^[\pL\s\-]+$/u',
                'last_name' => 'required|regex:/^[\pL\s\-]+$/u',
                'email' => 'required|email',
                'phone' => 'required|digits_between:10,13',
                'subject' => 'required',
                'messages' => 'required',
                'category_brand' => 'required',
                'g-recaptcha-response' => 'required'
            ],
               
            [
                'required' => __('Please fill in this field'),
                'regex' => __('Please match the requested format'),
                'email' => "Please include an @ in the email address. '" . $request->email . "' is missing an '@'",
                'g-recaptcha-response' => __('Check Your Security Code')
            ]);

            DB::beginTransaction();

            try {
                $data = FormService::create([
                    'category' => $request->category,
                    'country_code' => getLocaleCountryCode(),
                    'language_code' => getLocaleLanguageCode(),
                    'value' => request()->only('first_name',
                        'last_name',
                        'email',
                        'phone',
                        'subject',
                        'messages',
                        'category_brand',
                        'price'),
                ]);

                $contactData = FormService::find($data->id);

                Mail::to(
                    env(
                        'CUSTOMERCARE_EMAIL_RECIPIENT',
                        'customercare@modena.com'
                    )
                )
                ->send(new \App\Mail\ContactUsMail($contactData));

                if(env('CUSTOMERCARE_EMAIL_DEVMODE',false) == true) {
                    $bccs = explode(',', env('CUSTOMERCARE_EMAIL_BCC','dwiki.herdiansyah@modena.com'));

                    foreach ($bccs as $bcc) {
                        Mail::bcc($bcc)->send(new \App\Mail\ContactUsMail($contactData));
                    }
                }

                DB::commit();
                session()->flash('success', trans('Your information has been saved, please wait for a reply from our customer service'));
                return redirect()->intended(routeLocale('contact-us.index')); 
            } catch (\Exception $e) {

                DB::rollback();
                throw $e;
                session()->flash('error', trans('Form submit failed'));
                return redirect()->back()->withInput();
            }
        } elseif ($request->category == 'Product Register') {

            $request->validate([
                'last_name' => 'required',
                'address' => 'required',
                'province_id' => 'required',
                'city_id' => 'required',
                'email' => 'required',
                'phone' => 'required|numeric',
                'brand' => 'required',
                'company' => 'required',
                'age_product' => 'required',
                'products' => 'required',
                'request_1' => 'required',
                'request_2' => 'required',
                'image' => 'required|image',
                'invoice' => 'nullable|mimes:jpg,jpeg,png,svg,doc,docx,pdf',
            ]);
            DB::beginTransaction();
            try {
                $data = FormService::create([
                    'category' => $request->category,
                    'value' => request()->only(
                        'first_name',
                        'last_name',
                        'address',
                        'province_id',
                        'city_id',
                        'email',
                        'phone',
                        'brand',
                        'company',
                        'age_product',
                        'products',
                        'request_1',
                        'request_2'),
                ]);

                $this->storeFile($request->file('image'), $data, 'image', "images/services/{$data->id}", 'image');

                if ($request->file('invoice')) {
                    $this->storeFile($request->file('invoice'), $data, 'invoice', "images/services/{$data->id}", 'invoice');
                }
                DB::commit();
                session()->flash('success', trans('Your information has been saved, please wait for a reply from our customer service'));

                return redirect()->routeLocale('account.product-registration');

            } catch (\Exception $e) {

                DB::rollback();
                throw $e;
                session()->flash('error', trans('Form submit failed'));
                return redirect()->back()->withInput();
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

    public function update(Request $request, $id)
    {
        $list = FormService::where('id', $id)->first();
        $data = [
            'name' => $list->value['name'],
            'address' => $list->value['address'],
            'province' => $list->value['province'],
            'city' => $list->value['city'],
            'email' => $list->value['email'],
            'phone' => $list->value['phone'],
            'products' => [
                'title' => $list->value['products']['title'],
                'qty' => $request->qty,
            ],
        ];

        FormService::where('id', $id)->update(['value' => $data]);

        return response()->json($id);
    }

    public function duration(Request $request, $id)
    {
        $list = FormService::where('id', $id)->first();
        $data = [
            'name' => $list->value['name'],
            'address' => $list->value['address'],
            'province' => $list->value['province'],
            'city' => $list->value['city'],
            'email' => $list->value['email'],
            'phone' => $list->value['phone'],
            'products' => [
                'title' => $list->value['products']['title'],
                'qty' => $list->value['products']['qty'],
                'duration' => $request->duration,
            ],
        ];

        FormService::where('id', $id)->update(['value' => $data]);

        return response()->json($id);
    }

    public function updateImage(Request $request, FormService $data)
    {
        $this->storeFile($request->file('image'), $data, 'image', "images/services/{$data->id}", 'image');

        if ($request->file('invoice')) {
            $this->storeFile($request->file('invoice'), $data, 'invoice', "images/services/{$data->id}", 'invoice');
        }
    }

}
