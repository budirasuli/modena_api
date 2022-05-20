<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormTradeIn extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'form_trade_in';

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
        'title_code',
        'name',
        'phone',
        'email',
        'address',
        'data_source',
    ];

    public function formTradeInProductInformation()
    {
       return $this->hasMany(FormTradeInProductInformation::class, 'id_form_trade_in','id');
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
