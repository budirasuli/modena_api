<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProductSeries extends Model
{
    use SoftDeletes;

    protected $table = 'product_series';

    protected $fillable = [
        'id_product_series_id', 
        'series_name',
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

    public function setSeriesNameAttribute($series_name){
        $this->attributes['room_name'] = $series_name;

        if (strlen($series_name) != strlen(utf8_decode($series_name))) {
            $slug = html_entity_decode(strtolower($series_name), ENT_QUOTES, 'UTF-8');
            $slug = preg_replace('~[^\\pL\d]+~u', '_', $slug);
            $slug = trim($slug, '_');
            $this->attributes['slug']             = $slug;
            // $this->attributes['slug']             = preg_replace('/\s+/u', '', $title);
        } else {
            $this->attributes['slug']             = Str::slug(strtolower($series_name) , "_");
        }
    }
    public function productSeriesId()
    {
        return $this->hasOne(ProductSeriesId::class,'id','id_product_series_id');
    }
}
