<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

# Models
use App\Model\MasterProvince;
use App\Model\MasterCity;
use App\Model\Province;
use App\Model\Cities;

class KitchenDesignMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->data->is_migration == 1) {
            $getProvince = Province::where('id',$this->data->value['province'])->get()->first();
            $province    = !empty($getProvince) ? $getProvince->name : '-';

            $getCity     = Cities::where('id', $this->data->value['city'])->get()->first();
            $city        = !empty($getCity) ? $getCity->name : '-';
        } else {
            $getProvince = MasterProvince::where('id_province',$this->data->value['province'])->get()->first();
            $province    = !empty($getProvince) ? $getProvince->province_name : '-';

            $getCity     = MasterCity::where('id_city', $this->data->value['city'])->get()->first();
            $city        = !empty($getCity) ? $getCity->city_name : '-';
        }

        return $this->markdown('emails.cucine')
                    ->subject("New MODENA Cucine Report ".date("jS \of F Y"))
                    ->with([
                        'data'     => $this->data,
                        'province' => $province,
                        'city'     => $city
                    ]);
    }
}
