<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormClaimCashbackProductInformation extends Model
{
    use HasFactory;

	protected $table = 'form_cashback_claims_product_informations';

	protected $fillable = [
		'id_form_claim_cashback',
		'serial_number',
		'product_name',
		'sku',
		'purchase_date',
		'store_name',
		'created_at',
		'updated_at',
	];

	public function formClaimCashback()
    {
        return $this->hasOne(FormClaimCashback::class, 'id', 'id_form_claim_cashback');
    }

	public function invoice_warranty()
    {
        // return $this->morphOne(Media::class, 'mediable')->where('content_type', 'warranty');
		return $this->morphOne('App\Models\Media', 'mediable')->where('content_type', 'invoice_warranty');
    }
}
