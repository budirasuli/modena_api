<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormClaimCashback extends Model
{
    use HasFactory;

	protected $table = 'form_cashback_claims';

	protected $fillable = [
		'csms_customer_id',
		'csms_address_id',
		'csms_phone_id',
		'country_code',
		'cashback_type',
		'name',
		'phone',
		'email',
		'identity_card_number',
		'address',
		'id_province',
		'id_city',
		'id_district',
		'id_village',
		'postal_code',
		'bank_account',
		'bank_account_number',
		'bank_account_name',
		'created_at',
		'updated_at',
		'data_source',
	];

	public function formClaimCashbackProductInformation()
    {
       return $this->hasMany(FormClaimCashbackProductInformation::class, 'id_form_claim_cashback','id');
    }

	public function id_card()
    {
        // return $this->morphOne(Media::class, 'mediable')->where('content_type', 'warranty');
		return $this->morphOne('App\Models\Media', 'mediable')->where('content_type', 'id_card');
    }

	public function selfie_with_id_card()
    {
        // return $this->morphOne(Media::class, 'mediable')->where('content_type', 'warranty');
		return $this->morphOne('App\Models\Media', 'mediable')->where('content_type', 'selfie_with_id_card');
    }
}
