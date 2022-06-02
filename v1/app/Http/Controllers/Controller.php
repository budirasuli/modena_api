<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Http;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function checkDataKonsumentCsms($id)
    {
        $request = Http::withHeaders(['modena_keys' => env('CSMS_API_KEY')])
        ->post(env('CSMS_API_URL_CUSTOMER'), ['flag' => 'Check', 'phone_no' => $id])
        ->json();

        return $request;
    }

    protected function simpanDataKonsumenCsms(array $params=[])
    {
        $params = array_merge(['flag' => 'Insert'], $params);

        $request = Http::withHeaders(['modena_keys' => env('CSMS_API_KEY')])
        ->post(env('CSMS_API_URL_CUSTOMER'), $params)
        ->json();

        return $request;
    }
}
