<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class ProductSubCategory extends Model
{
    use SoftDeletes;

    protected $table = 'product_sub_category';

    protected $fillable = [
        'id_product_sub_category_id', 
        'id_product_category_id', 
        'id_product_segment_id',
        'sub_category_name',
        'sort',
        'active',
        'is_tradein',
        'is_rental',
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
    public function setSubCategoryNameAttribute($sub_category_name){
        $this->attributes['sub_category_name'] = $sub_category_name;

        if (strlen($sub_category_name) != strlen(utf8_decode($sub_category_name))) {
            $slug = html_entity_decode(strtolower($sub_category_name), ENT_QUOTES, 'UTF-8');
            $slug = preg_replace('~[^\\pL\d]+~u', '_', $slug);
            $slug = trim($slug, '_');
            $this->attributes['slug']             = $slug;
            // $this->attributes['slug']             = preg_replace('/\s+/u', '', $title);
        } else {
            $this->attributes['slug']             = Str::slug(strtolower($sub_category_name) , "_");
        }
    }

    public function productSubCategoryId()
    {
        return $this->hasOne(ProductSubCategoryId::class,'id','id_product_sub_category_id');
    }
    
    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class,'id','id_product_category_id');
    }
}
