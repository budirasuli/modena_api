<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDetail extends Model
{
    use SoftDeletes;

    protected $table = 'product_detail';
  
    protected $fillable = [
        'id_product_master_id', 
        'header_description',
        'description',
        'feature',
        'including',
        'tagline',
        'id_color_id',
        'id_tags_id',
        'price',
        'rating',
        'is_preorder',
        'is_rental',
        'sort',
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


    public function productMasterId()
    {
        return $this->hasOne(ProductMasterId::class,'id','id_product_master_id');
    }
    public function image()
    {
        return $this->morphOne('App\Model\Media', 'mediable')->where('content_type', 'image');
    }
}
