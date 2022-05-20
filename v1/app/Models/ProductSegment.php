<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Eloquent\HasMetaData;

class ProductSegment extends Model
{
    use SoftDeletes;

    protected $table = 'product_segment';

    protected $fillable = [
        'id_product_segment_id', 
        'segment_name',
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

    public function setSegmentNameAttribute($segment_name){
        $this->attributes['room_name'] = $segment_name;

        if (strlen($segment_name) != strlen(utf8_decode($segment_name))) {
            $slug = html_entity_decode(strtolower($segment_name), ENT_QUOTES, 'UTF-8');
            $slug = preg_replace('~[^\\pL\d]+~u', '_', $slug);
            $slug = trim($slug, '_');
            $this->attributes['slug']             = $slug;
            // $this->attributes['slug']             = preg_replace('/\s+/u', '', $title);
        } else {
            $this->attributes['slug']             = Str::slug(strtolower($segment_name) , "_");
        }
    }

    public function productSegmentId()
    {
        return $this->hasOne(ProductSegmentId::class,'id','id_product_segment_id');
    }

    public function productCategory()
    {
        return $this->hasMany('App\Model\ProductCategory','id_product_segment_id','id');
    }
}
