<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FormRentalProduct extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'form_rental_product';

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
        'id_form_rental',
        'id_product_master_id',
        'name',
        'qty'
    ];


    public function formRental()
    {
        return $this->hasOne(FormRental::class,'id','id_form_rental');
    }
}
