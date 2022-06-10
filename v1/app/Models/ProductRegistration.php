<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Str;
use function str_replace;
use function title_case;

/**
 * App\Model\ProductRegistration
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $address
 * @property string $province_id
 * @property string $city_id
 * @property string $email
 * @property string $phone
 * @property string $brand
 * @property string $product_name
 * @property string $store_name
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read City $city
 * @property-read Province $province
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration whereAgeProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration whereBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration whereProvinceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration whereStoreName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Media|null $warranty
 * @property-read Media|null $invoice
 * @property string $serial_number
 * @property string $purchase_date
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration wherePurchaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration whereSerialNumber($value)
 * @property int $user_id
 * @property-read \App\Model\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration whereUserId($value)
 * @property string|null $out_warranty_reason
 * @property string|null $rejected_reason
 * @property-read mixed $status_display
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration whereOutWarrantyReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRegistration whereRejectedReason($value)
 */
class ProductRegistration extends Model
{
    const IN_REVIEW = 'in_review';
    const IN_WARRANTY = 'in_warranty';
    const OUT_WARRANTY = 'out_warranty';
    const REJECTED = 'rejected';
    public $fillable = [
		'user_id',
		'country_code',
        'first_name',
        'last_name',
        'address',
        'province_id',
        'city_id',
		'district_id',
		'subdistrict_id',
        'postal_code',
        'email',
        'phone',
        'brand',
        'product_name',
        'purchase_date',
        'serial_number',
        'store_name',
        'data_source',
        'product_id',
        'category_id',
    ];

    public $appends = [
        'status_display',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function warranty()
    {
        // return $this->morphOne(Media::class, 'mediable')->where('content_type', 'warranty');
		return $this->morphOne('App\Models\Media', 'mediable')->where('content_type', 'warranty');
    }

    public function invoice()
    {
        // return $this->morphOne(Media::class, 'mediable')->where('content_type', 'invoice');
		return $this->morphOne('App\Models\Media', 'mediable')->where('content_type', 'invoice');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusDisplayAttribute()
    {
        return Str::title(
            str_replace('_', ' ', $this->status)
        );
    }
}
