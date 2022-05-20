<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Eloquent\HasMetaData;



/**
 * App\Model\MasterCategory
 *
 * @property int $id
 * @property int $active
 * @property string $name
 * @property int $master_series_id
 * @property int $position
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * * @property-read \App\Model\MetaData $meta
 * @method static \Illuminate\Database\Eloquent\Builder|MasterCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MasterCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MasterCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|MasterCategory whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MasterCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MasterCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MasterCategory whereMasterSeriesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MasterCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MasterCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MasterCategory extends Model
{
    use HasMetaData;
    protected $table = 'master_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'active', 'name', 'master_series_id', 'position'
    ];

    public function series()
    {
        return $this->belongsTo(MasterSeries::class, 'master_series_id', 'id');
    }

    public function products()
    {
        return $this->hasMany('App\Model\Product', 'category_id');
    }

    public function subCategories()
    {
        return $this->hasMany('App\Model\MasterSubCategory', 'master_category_id', 'id');
    }
}
