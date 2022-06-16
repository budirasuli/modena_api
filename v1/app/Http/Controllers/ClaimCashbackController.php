<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use File;

use App\Models\FormClaimCashback;
use App\Models\FormClaimCashbackProductInformation;
use App\Models\ProductRegistration;
use App\Models\MasterPostalCode;

class ClaimCashbackController extends Controller
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
			$cashbackProducts = FormClaimCashback::with([
					'formClaimCashbackProductInformation',
					'id_card',
					'selfie_with_id_card'
				])
				->where('phone', $request->phone)
				->where('email', $request->email)
				->get()
				->toArray();

			foreach($cashbackProducts as $key => $val){
				if(!empty($val['id_card'])){
					$cashbackProducts[$key]['id_card'] = Storage::disk('sftp')->url($val['id_card']['directory']);
				}

				if(!empty($val['selfie_with_id_card'])){
					$cashbackProducts[$key]['selfie_with_id_card'] = Storage::disk('sftp')->url($val['selfie_with_id_card']['directory']);
				}
			}

			$response = [
				'success'=> true,
				'message'=> 'List Claim Cashback',
				'data'=> $cashbackProducts
			];
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
				// VALIDATION RULES
				$rules = [
                    'cashback_type' => 'required',
                    'name' => 'required|regex:/^[\pL\s\-]+$/u',
                    'email' => 'required|email',
                    'phone' => 'required|min:10|numeric',
					'id_card' => 'required|url',
					'selfie_with_id_card' => 'required|url'
                ];

				if($request->cashback_type == 'Free Item'){
					$rules['address'] = 'required';
					$rules['postal_code'] = 'required';
				}else{
					$rules['bank_account'] = 'required';
					$rules['bank_account_number'] = 'required|min:10|numeric';
					$rules['bank_account_name'] = 'required|regex:/^[\pL\s\-]+$/u';
				}

				if(!empty($request->products)){
					foreach($request->products as $key => $val){
						$rules['products.'.$key.'.serial_number'] = 'required';
						$rules['products.'.$key.'.product_name'] = 'required';
						$rules['products.'.$key.'.sku'] = 'required';
						$rules['products.'.$key.'.purchase_date'] = 'required';
						$rules['products.'.$key.'.store_name'] = 'required';
					}
				}else{
					$response = [
						'success' => false,
						'message' => 'Products are required'
					];
				}

				$validator = validator()->make($request->all(), $rules);

				// VALIDATION CHECK
				if($validator->fails()){
					$errorMessage = $validator->errors()->first();
					$response = [
						'success' => false,
						'message' => $errorMessage
					];
				}else{
					if($request->cashback_type == 'Free Item'){
						// FREE ITEM
						$locationBasedOnPostalCode = MasterPostalCode::select(
								'master_postal_code.postal_code',
								'master_village.id_village',
								'master_district.id_district',
								'master_city.id_city',
								'master_city.id_province'
							)
							->join('master_village', 'master_village.id_village', 'master_postal_code.id_village')
							->join('master_district', 'master_district.id_district', 'master_village.id_district')
							->join('master_city', 'master_city.id_city', 'master_district.id_city')
							->where('postal_code', $request->postal_code)
							->first();

						$id_village     = $locationBasedOnPostalCode->id_village;
						$id_district    = $locationBasedOnPostalCode->id_district;
						$id_city        = $locationBasedOnPostalCode->id_city;
						$id_province    = $locationBasedOnPostalCode->id_province;

						$formClaimCashback = new FormClaimCashback();

						$data = [
							'csms_customer_id' => $request->csms_customer_id,
							'csms_address_id'  => $request->csms_address_id,
							'csms_phone_id'    => $request->csms_phone_id,
							'country_code'     => $request->country_code,
							'cashback_type'    => 'Free Item',
							'name'             => $request->name,
							'phone'            => $request->phone,
							'email'            => $request->email,
							'address'		   => $request->address,
							'id_province'      => $id_province,
							'id_city' 		   => $id_city,
							'id_district'	   => $id_district,
							'id_village'	   => $id_village,
							'postal_code'	   => $request->postal_code,
							'data_source'      => 'CHAT',
						];

						$formClaimCashback->fill($data)
							->save();

						$id = $formClaimCashback->id;

						// ID CARD UPLOAD
						$id_card = $request->id_card;
						$id_card_filename = substr($id_card, strrpos($id_card, '/') + 1);
						$id_card_image = file_get_contents($request->id_card);

						file_put_contents(storage_path('temp/'.$id_card_filename), $id_card_image);
						$file_id_card = File::get(storage_path('temp/'.$id_card_filename));

						if (!empty($id_card_image)) {
                            $this->storeFile($id_card_image, $formClaimCashback, 'id_card', "images/id_card/{$id}", 'id_card', storage_path('temp/'.$id_card_filename));

							unlink(storage_path('temp/'.$id_card_filename));
                        }

						// SELFIE WITH ID CARD UPLOAD
						$selfie_with_id_card = $request->selfie_with_id_card;
						$selfie_with_id_card_filename = substr($selfie_with_id_card, strrpos($selfie_with_id_card, '/') + 1);
						$selfie_with_id_card_image = file_get_contents($request->selfie_with_id_card);

						file_put_contents(storage_path('temp/'.$selfie_with_id_card_filename), $selfie_with_id_card_image);
						$file_selfie_with_id_card = File::get(storage_path('temp/'.$selfie_with_id_card_filename));

						if (!empty($selfie_with_id_card_image)) {
                            $this->storeFile($selfie_with_id_card_image, $formClaimCashback, 'selfie_with_id_card', "images/selfie_with_id_card/{$id}", 'selfie_with_id_card', storage_path('temp/'.$selfie_with_id_card_filename));

							unlink(storage_path('temp/'.$selfie_with_id_card_filename));
                        }

						foreach($request->products as $key => $val){
							$formClaimCashbackProductInformation = new FormClaimCashbackProductInformation();

							$dataProductInformation = [
								'id_form_claim_cashback' => $id,
								'serial_number' => $val['serial_number'],
								'product_name' => $val['product_name'],
								'sku' => $val['sku'],
								'purchase_date' => $val['purchase_date'],
								'store_name' => $val['store_name'],
							];

							$formClaimCashbackProductInformation->fill($dataProductInformation)->save();

							$idInfo = $formClaimCashbackProductInformation->id;

							// INVOICE + WARRANTY UPLOAD
							$invoice_and_warranty = $val['invoice_and_warranty'];
							$invoice_and_warranty_filename = substr($invoice_and_warranty, strrpos($invoice_and_warranty, '/') + 1);
							$invoice_and_warranty_image = file_get_contents($val['invoice_and_warranty']);

							file_put_contents(storage_path('temp/'.$invoice_and_warranty_filename), $invoice_and_warranty);
							$file_invoice_and_warranty = File::get(storage_path('temp/'.$invoice_and_warranty_filename));

							if (!empty($invoice_and_warranty_image)) {
								$this->storeFile($invoice_and_warranty_image, $formClaimCashbackProductInformation, 'invoice_warranty', "images/invoice_warranty/{$id}/{$idInfo}", 'invoice_warranty', storage_path('temp/'.$invoice_and_warranty_filename));

								unlink(storage_path('temp/'.$invoice_and_warranty_filename));
							}

							$checkProductRegistration = ProductRegistration::where('email', $request->email)
								->where('phone', $request->phone)
								->where('serial_number', $val['serial_number'])
								->first();

							if(empty($checkProductRegistration)){
								$productRegistration = new ProductRegistration();

								$dataProductRegistration = [
									'first_name' => $request->name,
									'email' => $request->email,
									'phone' => $request->phone,
									'address' => $request->address,
									'province_id' => $id_province,
									'city_id' => $id_city,
									'district_id' => $id_district,
									'subdistrict_id' => $id_village,
									'postal_code' => $request->postal_code,
									'brand' => 'MODENA',
									'product_name' => $val['product_name'],
									'serial_number' => $val['serial_number'],
									'purchase_date' => $val['purchase_date'],
									'store_name' => $val['store_name'],
									'country_code' => $request->country_code,
									'data_source' => 'CLAIM CASHBACK',
								];

								$productRegistration->fill($dataProductRegistration)->save();

								$invoice = $val['invoice_and_warranty'];
								$invoice_filename = substr($invoice, strrpos($invoice, '/') + 1);
								$invoice_image = file_get_contents($val['invoice_and_warranty']);

								file_put_contents(storage_path('temp/'.$invoice_filename), $invoice_image);

								$file = File::get(storage_path('temp/'.$invoice_filename));

								if (!empty($invoice_image)) {
									$this->storeFile($invoice_image, $productRegistration, 'invoice', "invoices/{$productRegistration->id}", 'invoice', storage_path('temp/'.$invoice_filename));

									unlink(storage_path('temp/'.$invoice_filename));
								}
							}
						}

						$response = [
							'success' => true,
							'message' => 'Cashback input success'
						];
					}else{
						// TRANSFER
						$formClaimCashback = new FormClaimCashback();

						$data = [
							'csms_customer_id' => $request->csms_customer_id,
							'csms_address_id'  => $request->csms_address_id,
							'csms_phone_id'    => $request->csms_phone_id,
							'country_code'     => $request->country_code,
							'cashback_type'    => 'Transfer',
							'name'             => $request->name,
							'phone'            => $request->phone,
							'email'            => $request->email,
							'bank_account'     => $request->bank_account,
							'bank_account_number' 	=> $request->bank_account_number,
							'bank_account_name'		=> $request->bank_account_name,
							'data_source'      		=> 'CHAT',
						];

						$formClaimCashback->fill($data)
							->save();

						$id = $formClaimCashback->id;

						// ID CARD UPLOAD
						$id_card = $request->id_card;
						$id_card_filename = substr($id_card, strrpos($id_card, '/') + 1);
						$id_card_image = file_get_contents($request->id_card);

						file_put_contents(storage_path('temp/'.$id_card_filename), $id_card_image);
						$file_id_card = File::get(storage_path('temp/'.$id_card_filename));

						if (!empty($id_card_image)) {
                            $this->storeFile($id_card_image, $formClaimCashback, 'id_card', "images/id_card/{$id}", 'id_card', storage_path('temp/'.$id_card_filename));

							unlink(storage_path('temp/'.$id_card_filename));
                        }

						// SELFIE WITH ID CARD UPLOAD
						$selfie_with_id_card = $request->selfie_with_id_card;
						$selfie_with_id_card_filename = substr($selfie_with_id_card, strrpos($selfie_with_id_card, '/') + 1);
						$selfie_with_id_card_image = file_get_contents($request->selfie_with_id_card);

						file_put_contents(storage_path('temp/'.$selfie_with_id_card_filename), $selfie_with_id_card_image);
						$file_selfie_with_id_card = File::get(storage_path('temp/'.$selfie_with_id_card_filename));

						if (!empty($selfie_with_id_card_image)) {
                            $this->storeFile($selfie_with_id_card_image, $formClaimCashback, 'selfie_with_id_card', "images/selfie_with_id_card/{$id}", 'selfie_with_id_card', storage_path('temp/'.$selfie_with_id_card_filename));

							unlink(storage_path('temp/'.$selfie_with_id_card_filename));
                        }

						foreach($request->products as $key => $val){
							$formClaimCashbackProductInformation = new FormClaimCashbackProductInformation();

							$dataProductInformation = [
								'id_form_claim_cashback' => $id,
								'serial_number' => $val['serial_number'],
								'product_name' => $val['product_name'],
								'sku' => $val['sku'],
								'purchase_date' => $val['purchase_date'],
								'store_name' => $val['store_name'],
							];

							$formClaimCashbackProductInformation->fill($dataProductInformation)->save();

							$idInfo = $formClaimCashbackProductInformation->id;

							// INVOICE + WARRANTY UPLOAD
							$invoice_and_warranty = $val['invoice_and_warranty'];
							$invoice_and_warranty_filename = substr($invoice_and_warranty, strrpos($invoice_and_warranty, '/') + 1);
							$invoice_and_warranty_image = file_get_contents($val['invoice_and_warranty']);

							file_put_contents(storage_path('temp/'.$invoice_and_warranty_filename), $invoice_and_warranty);
							$file_invoice_and_warranty = File::get(storage_path('temp/'.$invoice_and_warranty_filename));

							if (!empty($invoice_and_warranty_image)) {
								$this->storeFile($invoice_and_warranty_image, $formClaimCashbackProductInformation, 'invoice_warranty', "images/invoice_warranty/{$id}/{$idInfo}", 'invoice_warranty', storage_path('temp/'.$invoice_and_warranty_filename));

								unlink(storage_path('temp/'.$invoice_and_warranty_filename));
							}

							$checkProductRegistration = ProductRegistration::where('email', $request->email)
								->where('phone', $request->phone)
								->where('serial_number', $val['serial_number'])
								->first();

							if(empty($checkProductRegistration)){
								$productRegistration = new ProductRegistration();

								$dataProductRegistration = [
									'first_name' => $request->name,
									'email' => $request->email,
									'phone' => $request->phone,
									'brand' => 'MODENA',
									'product_name' => $val['product_name'],
									'serial_number' => $val['serial_number'],
									'purchase_date' => $val['purchase_date'],
									'store_name' => $val['store_name'],
									'country_code' => $request->country_code,
									'data_source' => 'CLAIM CASHBACK',
								];

								$productRegistration->fill($dataProductRegistration)->save();

								$invoice = $val['invoice_and_warranty'];
								$invoice_filename = substr($invoice, strrpos($invoice, '/') + 1);
								$invoice_image = file_get_contents($val['invoice_and_warranty']);

								file_put_contents(storage_path('temp/'.$invoice_filename), $invoice_image);

								$file = File::get(storage_path('temp/'.$invoice_filename));

								if (!empty($invoice_image)) {
									$this->storeFile($invoice_image, $productRegistration, 'invoice', "invoices/{$productRegistration->id}", 'invoice', storage_path('temp/'.$invoice_filename));

									unlink(storage_path('temp/'.$invoice_filename));
								}
							}
						}

						$response = [
							'success' => true,
							'message' => 'Cashback input success'
						];
					}
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
