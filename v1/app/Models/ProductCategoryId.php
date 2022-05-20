<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategoryId extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_category_id';

    
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
        'image',
        'updated_at'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    public function ProductCategory()
    {
        return $this->hasMany(ProductCategory::class, 'id_product_category_id', 'id');
    }

    public function image()
    {
        return $this->morphOne('App\Model\Media', 'mediable')->where('content_type', 'image');
    }
}
