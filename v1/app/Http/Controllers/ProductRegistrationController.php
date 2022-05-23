<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductRegistration;
use Illuminate\Support\Facades\Storage;

class ProductRegistrationController extends Controller
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
                $transaction = DB::table('product_registrations')->select('*')
                ->where('country_code',$request->country_code)
                ->where('language_code',$request->language_code)
                ->get();
                $response = [
                    'success'=> true,
                    'message'=> 'List Product',
                    'data'=> $transaction
                ];
                return $response;
            }
        }
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
                    'id_province' => ['required'],
                    'id_city' => ['required'],
                    'email' => ['required', 'email'],
                    'phone' => ['required','min:8'],
                    // 'brand' => ['required'],
                    'product_name' => ['required'],
                    'serial_number' => ['required'],
                    'purchase_date' => ['required', 'date'],
                    'store_name' => ['required'],
                    'invoice' => ['required', 'mimes:jpg,jpeg,png,svg,doc,docx,pdf'],
                    'warranty' => ['required', 'mimes:jpg,jpeg,png,svg,doc,docx,pdf'],
                ]);
                DB::beginTransaction();
                try {
                    $data = new ProductRegistration();
                    $data->user_id = Auth::id();
                    $data->fill($request->all());
                    $data->save();
                    if ($request->file('invoice')) {
                        $this->storeFile($request->file('invoice'), $data, 'invoice', "invoices/{$data->id}", 'invoice');
                    }
                    if ($request->file('warranty')) {
                        $this->storeFile($request->file('warranty'), $data, 'warranty', "warranties/{$data->id}", 'warranty');
                    }
                    DB::commit();
                    session()->flash('success', trans('Your information has been saved, please wait for a reply from our customer service'));

                    return redirect()->route('account.product-registration');

                } catch (\Exception $e) {

                    DB::rollback();
                    throw $e;
                    session()->flash('error', trans('Form submit failed'));
                    return redirect()->back()->withInput();
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
