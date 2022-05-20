<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    
    protected $table = 'form_rental';
    protected $primaryKey = 'id';
    protected $fillable = [
        'no_submission',
        'csms_customer_id',
        'csms_address_id',
        'csms_phone_id',
        'country_code',
        'id_province',
        'id_city',
        'id_district',
        'id_village',
        'postal_code',
        'title_code',
        'name',
        'phone',
        'email',
        'address',
        'data_source',
    ];

    public function product()
    {
       return $this->hasMany(RentalProduct::class, 'id_form_rental','id');
    }
    public function province()
    {
        return $this->hasOne(MasterProvince::class,'id_province','id_province');
    }

    public function city()
    {
        return $this->hasOne(MasterCity::class,'id_city','id_city');
    }

    public function district()
    {
        return $this->hasOne(MasterDistrict::class,'id_district','id_district');
    }

    public function village()
    {
        return $this->hasOne(MasterVillage::class,'id_village','id_village');
    }
}
