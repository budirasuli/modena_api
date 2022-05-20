<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Eloquent\HasMetaData;

class ProductCategory extends Model
{
    use SoftDeletes;

    protected $table = 'product_category';

    protected $fillable = [
        'id_product_category_id', 
        'id_product_segment_id',
        'category_name',
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

    public function setCategoryNameAttribute($category_name){
        $this->attributes['room_name'] = $category_name;

        if (strlen($category_name) != strlen(utf8_decode($category_name))) {
            $slug = html_entity_decode(strtolower($category_name), ENT_QUOTES, 'UTF-8');
            $slug = preg_replace('~[^\\pL\d]+~u', '_', $slug);
            $slug = trim($slug, '_');
            $this->attributes['slug']             = $slug;
            // $this->attributes['slug']             = preg_replace('/\s+/u', '', $title);
        } else {
            $this->attributes['slug']             = Str::slug(strtolower($category_name) , "_");
        }
    }

    public function productCategoryId()
    {
        return $this->hasOne(ProductCategoryId::class,'id','id_product_category_id');
    }

    public function productSegment()
    {
        return $this->belongsTo(ProductSegment::class,'id','id_product_segment_id');
    }
    public function productSubCategory()
    {
        return $this->hasMany('App\Model\ProductSubCategory','id_product_category_id','id');
    }
}
