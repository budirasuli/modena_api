<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FormContractService extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'form_contract_service';

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
        'id_category',
        'brand',
        'product_model',
        'age_product',
        'requests',
    ];

    protected $casts = [
        'requests' => 'array'
    ];

    public function image()
    {
        return $this->morphOne('App\Model\Media', 'mediable')->where('content_type', 'image');
    }
}
