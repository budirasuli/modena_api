<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCenter extends Model
{
    //
    use SoftDeletes;

    protected $table = 'service_center';

    protected $fillable = [
        'id_service_center_id', 
        'introduction_title',
        'introduction_description',
        'country_code',
        'language_code',
        'created_by',
        'updated_by',
        'deleted_by',
        'image', 
    ];

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;


    public function serviceCenterId()
    {
        return $this->hasOne(ServiceCenterId::class,'id','id_service_center_id');
    }
}
