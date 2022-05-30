<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterBrand extends Model
{
    use SoftDeletes;
    
    protected $table = 'master_brand';

    public $timestamps = true;

    protected $hidden  = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'id',
        'brand_code',
        'brand_name'
    ];
}
