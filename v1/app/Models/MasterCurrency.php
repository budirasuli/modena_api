<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterCurrency extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master_currency';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_currency';

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
        'id_country',
        'currency_code',
        'currency_initial',
        'currency_name',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function country()
    {
        return $this->hasOne(MasterCountry::class, 'id_country', 'id_country');
    }
}
