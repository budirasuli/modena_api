<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterProvince extends Model
{
    use SoftDeletes;

     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master_province';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_province';

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

    protected $hidden  = [
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
        'id_user',
        'id_country',
        'province_name',
        'timezone',
        'latitude',
        'longitude',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
