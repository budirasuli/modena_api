<?php

namespace App\Models;

use App\Services\Cart\Traits\Cartable;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Model\ProductVariant
 *
 * @property int $id
 * @property int $product_id
 * @property int $color_id
 * @property string $sku
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Model\Color $color
 * @property-read \App\Model\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant whereColorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductVariant whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductVariant extends Model
{
    use Cartable;

    protected $table = 'product_variants';

    protected $fillable = ['color_id', 'sku'];

    public function getType()
    {
        return 'variant';
    }

    public function getName()
    {
        return $this->product->title;
    }

    public function getImage()
    {
        return optional($this->product->images->first())->path;
    }

    public function getPrice()
    {
        return $this->product->price;
    }

    public function product()
    {
        return $this->belongsTo('App\Model\Product', 'product_id');
    }

    public function color()
    {
        return $this->belongsTo('App\Model\Color', 'color_id');
    }

}
