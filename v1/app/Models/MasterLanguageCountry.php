<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class MasterLanguageCountry extends Model
{
    // use SoftDeletes;

     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master_language_country';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    // protected $primaryKey = '';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    // public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_language',
        'id_website_country'
    ];

    public function language()
    {
        return $this->hasOne(MasterLanguage::class,'id_language','id_language');
    }

    public function websiteCountry()
    {
        return $this->hasOne(WebsiteCountry::class, 'id_website_country', 'id_website_country');
    }
}
