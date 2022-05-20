<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterCountry extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master_country';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_country';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    // public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    protected $hidden = [
        'timezone',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_region',
        'id_timezone',
        'country_code',
        'country_name',
        'timezone',
        'latitude',
        'longitude',
        'phone_code',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function region()
    {
        return $this->hasOne(MasterRegion::class,'id_region','id_region');
    }

    public function masterTimezone()
    {
        return $this->hasOne(MasterTimezone::class,'id_timezone','id_timezone');
    }

    public function currency()
    {
        // return $this->hasMany(MasterCurrency::class,'id_country','id_country');
        return $this->hasOne(MasterCurrency::class,'id_country','id_country');
    }

    public function website()
    {
        // return $this->hasOne(WebsiteCountry::class,'country_code','subdomain');
        return $this->hasOne(WebsiteCountry::class,'id_country','id_country');
    }
}
