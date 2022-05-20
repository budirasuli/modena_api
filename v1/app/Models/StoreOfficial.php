<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreOfficial extends Model
{
    use SoftDeletes;

    protected $table = 'store_official';

    protected $fillable = [
        'id_store_category_id',
        'id_province_id',
        'id_city_id',
        'id_district_id',
        'id_village_id',
        'id_postal_id',
        'store_name',
        'phone_area',
        'phone',
        'contact_center',
        'pin_point',
        'longitude',
        'latitude',
        'store_address',
        'google_address',
        'active',
        'country_code',
        'language_code',
        'created_by',
        'updated_by',
        'deleted_by',
        'image',
    ];

    public $incrementing = true;

    protected $appends   = [
        'no_handphone'
    ];

    public function getNoHandphoneAttribute()
    {
        $no_handphone = $this->phone
                      ? $this->phone_area.$this->phone
                      : ' - ';
        return $no_handphone;
    }
}
