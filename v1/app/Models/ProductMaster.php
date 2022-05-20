<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProductMaster extends Model
{
    use SoftDeletes;

    protected $table = 'product_master';

    protected $fillable = [
        'id_product_master_id',
        'master_name',
        'id_product_series_id',
        'id_product_sub_category_id',
        'id_product_category_id',
        'id_product_segment_id',
        'sku',
        'slug',
        'type',
        'sort',
        'weight',
        'active',
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

}
