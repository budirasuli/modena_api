<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalProduct extends Model
{
    protected $table = 'form_rental_product';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_form_rental',
        'id_product_master_id',
        'name',
        'qty'
    ];

    public function formRental()
    {
        return $this->hasOne(Rental::class,'id','id_form_rental');
    }

}
