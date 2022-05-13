<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCenterId extends Model
{
    //
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'service_center_id';

    
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    // protected $primaryKey = 'id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    // public $incrementing = true;

    protected $fillable = [
        'image',
        'updated_at'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    public function serviceCenter()
    {
        return $this->hasMany(ServiceCenter::class, 'id_service_center_id', 'id');
    }

    public function image()
    {
        return $this->morphOne('App\Model\Media', 'mediable')->where('content_type', 'image');
    }
}
