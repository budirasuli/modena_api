<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FormRental extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'form_rental';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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
        'name',
        'phone',
        'email',
        'address',
    ];

    public function formRentalProduct()
    {
       return $this->hasMany(FormRentalProduct::class, 'id_form_rental','id');
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
