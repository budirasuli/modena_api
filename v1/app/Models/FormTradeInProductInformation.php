<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormTradeInProductInformation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'form_trade_in_product_information';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_form_trade_in',
        'brand',
        'id_category',
        'age_product',
        'requests',
    ];

    protected $casts = [
        'requests' => 'array'
    ];

    public function formTradeIn()
    {
        return $this->hasOne(FormTradeIn::class,'id','id_form_trade_in');
    }

    public function category()
    {
        return $this->hasOne(ProductSubCategory::class,'id_product_sub_category_id','id_category');
    }

    public function image()
    {
        return $this->morphOne('App\Models\Media', 'mediable')->where('content_type', 'image');
    }
}
