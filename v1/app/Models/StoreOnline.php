<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreOnline extends Model
{
    use SoftDeletes;

    protected $table = 'store_online';

    protected $fillable = [
        'key',
        'name',
        'link',
        'show_on_product',
        'country_code',
        'created_by',
        'updated_by',
        'deleted_by',
        'image',
    ];

    public $incrementing = true;

    public function publisher()
    {
        return $this->hasOne(Admin::class,'id','updated_by');
    }

    public function image()
    {
        return $this->morphOne('App\Model\Media', 'mediable')->where('content_type', 'image');
    }
}
