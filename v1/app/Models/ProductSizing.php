<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ProductSizing extends Model
{
    use SoftDeletes;

    protected $table = 'product_sizing';
  
    protected $fillable = [
        'id_product_master_id', 
        'sizing_type',
        'sizing_structure',
        'lenght',
        'width',
        'height',
        'depth',
        'thickness',
        'created_by',
        'updated_by',
        'deleted_by'
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
}
