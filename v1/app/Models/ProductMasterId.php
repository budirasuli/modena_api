<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductMasterId extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_master_id';

    
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    // protected $primaryKey = 'id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    // public $incrementing = true;

    protected $fillable = [
        'id',
        'image',
        'updated_at'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    public function ProductMaster()
    {
        return $this->hasMany(ProductMaster::class, 'id_product_master_id', 'id');
    }

    public function ProductDetail()
    {
        return $this->hasMany(ProductDetail::class, 'id_product_master_id', 'id');
    }

    public function image()
    {
        return $this->morphMany('App\Model\Media', 'mediable')->where('content_type', 'image');
    }
    public function manual()
    {
        return $this->morphMany('App\Model\Media', 'mediable')->where('content_type','like', 'manual_%');
    }
    public function imageBanner()
    {
        return $this->morphMany('App\Model\Media', 'mediable')->where('content_type', 'header_image');
    }
    public function imageHighlight()
    {
        return $this->morphOne('App\Model\Media', 'mediable')->where('content_type', 'highlight_image');
    }
    public function imageUsp()
    {
        return $this->morphMany('App\Model\Media', 'mediable')->where('content_type', 'usp_image');
    }
    public function imageFirst()
    {
        return $this->morphOne('App\Model\Media', 'mediable');   
    }
}
