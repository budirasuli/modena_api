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

    /*protected $appends   = [
        'price',
        'header_description',
        'features',
        'description',
        'whats_in_the_box'
    ];*/

    public function setTypeAttribute($type){
        $this->attributes["type"]=$type;

        if (strlen($type) != strlen(utf8_decode($type))) {
            $slug=$this->attributes['master_name']." ".$type;
            $slug = html_entity_decode(strtolower($slug), ENT_QUOTES, 'UTF-8');
            $slug = preg_replace('~[^\\pL\d]+~u', '-', $slug);
            $slug = trim($slug, '_');
            $this->attributes['slug']             = $slug;
            // $this->attributes['slug']             = preg_replace('/\s+/u', '', $title);
        } else {
            $slug=$this->attributes['master_name']." ".$type;
            $this->attributes['slug']             = Str::slug(strtolower($slug) , "-");
        }
    }

    public function getPriceAttribute()
    {
        if(isset($this->productDetail->price)) {
            return $this->productDetail->price;
        } else {
            return '';
        }
    }

    public function getHeaderDescriptionAttribute()
    {
        if(isset($this->productDetail->header_description)) {
            return $this->productDetail->header_description;
        } else {
            return '';
        }
    }

    public function getFeaturesAttribute()
    {
        if(isset($this->productDetail->feature)) {
            return $this->productDetail->feature;
        } else {
            return '';
        }
    }

    public function getDescriptionAttribute()
    {
        if(isset($this->productDetail->description)) {
            return $this->productDetail->description;
        } else {
            return '';
        }
    }

    public function getWhatsInTheBoxAttribute()
    {
        if(isset($this->productDetail->including)) {
            return $this->productDetail->including;
        } else {
            return '';
        }
    }

    public function productMasterId()
    {
        return $this->hasOne(ProductMasterId::class,'id','id_product_master_id');
    }

    public function productDetail()
    {
        return $this->hasOne(ProductDetail::class, 'id_product_master_id', 'id_product_master_id');
    }

    public function productSizing()
    {
        return $this->hasMany(ProductSizing::class, 'id_product_master_id', 'id_product_master_id');
    }

    public function productSizingCod()
    {
        return $this->hasOne(
            ProductSizing::class,
            'id_product_master_id',
            'id_product_master_id'
        )
        ->where('sizing_type', 'cod');
    }

    public function productSizingSize()
    {
        return $this->hasOne(
            ProductSizing::class,
            'id_product_master_id',
            'id_product_master_id'
        )
        ->where('sizing_type', 'size');
    }

    public function productSizingUncod()
    {
        return $this->hasOne(
            ProductSizing::class,
            'id_product_master_id',
            'id_product_master_id'
        )
        ->where('sizing_type', 'uncod');
    }

    public function productCatgory()
    {
        return $this->hasOne(ProductCategory::class, 'id_product_category_id', 'id_product_category_id');
    }

    public function productSubCategory()
    {
        return $this->hasOne(ProductSubCategory::class, 'id_product_sub_category_id', 'id_product_sub_category_id');
    }
}
