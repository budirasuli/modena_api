<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $table = 'master_faq';

    protected $fillable = [
        'id',
        'id_master_faq_id',
        'id_master_faq_category_id',
        'title',
        'description',
        'slug',
        'country_code',
        'language_code',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
