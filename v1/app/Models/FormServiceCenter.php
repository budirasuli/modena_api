<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FormServiceCenter extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'form_service_center';

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
        'csms_ron_number',
        'csms_customer_id',
        'csms_address_id',
        'csms_phone_id',
        'csms_problem_id',
        'csms_problem_desc',
        'product_identifier',
        'product_id',
        'serial_number',
        'product_name',
        'country_code',
        'id_province',
        'id_city',
        'id_district',
        'id_village',
        'postal_code',
        'phone',
        'email',
        'title_code',
        'first_name',
        'last_name',
        'address',
        'service_center',
        'technician',
        'explanation',
        'status',
        'date',
    ];
}
