<?php

namespace App\Http\Controllers;

use App\Model\FormRental;
use App\Model\FormRentalProduct;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                'message'=> 'List Rental'
            ];
            return $response;
            
        }else{
            $api_key = DB::table('api_clients')->select('*')->where('api_token', $token)->first();
            if (!$api_key){
                $response = [
                    'success'=> false,
                    'message'=> 'List Rental'
                ];
                return $response; 
            }else{

                if($request->phone){
                    // Rental List by phone
                    $transaction = DB::table('form_rental')->select('*')
                    ->where('country_code',$request->country_code)
                    ->where('phone',$request->phone)
                    ->get();
                    $response = [
                        'success'=> true,
                        'message'=> 'List Rental By Phone',
                        'data'=> $transaction
                    ];
                    return $response; 

                }else if($request->email){
                    // Rental List by email
                    $transaction = DB::table('form_rental')->select('*')
                    ->where('country_code',$request->country_code)
                    ->where('email',$request->email)
                    ->get();
                    $response = [
                        'success'=> true,
                        'message'=> 'List Rental By Email',
                        'data'=> $transaction
                    ];
                    return $response; 
                }else{
                // Rental List
                $rental = DB::table('form_rental')->select('*')
                ->where('country_code',$request->country_code)
                ->get();

                // $rental_product = DB::table('form_rental')->select('*')
                // ->where('id_form_rental',$rental->id)
                // ->get();

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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

        // foreach ($request->product as $key => $product) {
        //     $rules["product.$key.*"]                    = ['required'];
        //     $rules["product.$key.id_product_master_id"] = ['required'];
        //     // $rules["product.$key.name"]                 = ['required'];
        //     $rules["product.$key.qty"]                  = ['required','min:1'];

        //     $attributes["product.$key.id_product_master_id"] = "Product";
        //     // $attributes["product.$key.name"]                 = "Product Name";
        //     $attributes["product.$key.qty"]                  = "Quantity";
        // }

        $request->validate($rules, $messages, $attributes);

        $no_submission = 'RENT-'.substr($request->phone, -4).'-'.date('YmdHis');
        
        $response = [
            'success'=> false,
            'message'=> 'Cant save Your Rental Order'
        ];
        return $request;
       
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
