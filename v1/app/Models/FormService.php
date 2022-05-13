<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Model\FormService
 *
 * @property int $id
 * @property string $category
 * @property string|null $references_key
 * @property array $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Model\Media|null $image
 * @property-read \App\Model\Media|null $invoice
 * @property-read \App\Model\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|FormService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormService query()
 * @method static \Illuminate\Database\Eloquent\Builder|FormService whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormService whereReferencesKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormService whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormService whereValue($value)
 * @mixin \Eloquent
 */
class FormService extends Model
{
    protected $table = 'form_service';

    protected $fillable = [
        'category', 'value', 'picture', 'invoice', 'references_key', 'language_code', 'country_code'
    ];

    protected $casts = [
        'value' => 'array'
    ];

    public function image()
    {
        return $this->morphOne('App\Model\Media', 'mediable')->where('content_type', 'image');
    }

    public function invoice()
    {
        return $this->morphOne('App\Model\Media', 'mediable')->where('content_type', 'invoice');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
