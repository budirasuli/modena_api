<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Classes\Helper;

class ProductTag extends Model
{
    use SoftDeletes;

    protected $table = 'product_tags';
    protected $fillable = [
        'id', 
        'id_tag', 
        'title',
        'description',
        'sequence', 
        'country_code', 
        'language_code',
        'created_by', 
        'updated_by', 
        'deleted_by', 
        'image'
    ];

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    protected $isUpdate;

    protected $oldSortValue = 0;

    protected $newSortValue = 0;

    protected $section;

    public function save(array $options = [])
    {
        $this->section = isset($options['section']) ? $options['section'] : '';

        $this->checkStatusSave();
        
        parent::save();

        // set after save
        $this->afterSave();
    }

    public function delete()
    {
        parent::delete();

        $this->afterDelete();
    }

    protected function checkStatusSave()
    {
        if(!empty($this->id)) {
            $this->isUpdate     = true;
            $this->oldSortValue = $this->getOriginal('sequence');
            $this->newSortValue = $this->sequence;
        } else {
            $this->isUpdate = false;
        }
    }

    protected function afterSave()
    {
        if($this->section != 'delete') {
            if($this->isUpdate == true) {
                $this->updateSort();
            } else {
                $this->generateSort();
            }
        }

        return $this;
    }

    protected function afterDelete()
    {
        $this->resetSort();
    }

    protected function generateSort()
    {
        // generate sequence
        if(empty($this->sequence)) {
            $queryGet = $this->select(
                'sequence'
            )
            ->where('country_code', $this->country_code)
            ->where('language_code', $this->language_code)
            ->orderByDesc('sequence')
            ->get()
            ->first();

            $sequence = (!empty($queryGet))
                  ? $queryGet->sequence+1
                  : 1;

            $this
                ->where('id', $this->id)
                ->update([
                    'sequence' => $sequence
                ]);

        } else {
            $queryGet = $this->select(
                'id',
                'sequence'
            )
            ->where('country_code', $this->country_code)
            ->where('language_code', $this->language_code)
            ->where('sequence', '>=', $this->sequence)
            ->where('id_tag', '!=', $this->id_tag)
            ->orderBy('sequence')
            ->get()
            ->toArray();

            $sequence = $this->sequence + 1;
            foreach ($queryGet as $val) {
                $this
                    ->where('id', $val['id'])
                    ->update([
                        'sequence' => $sequence
                    ]);

                $sequence++;
            }
        }
    }

    protected function updateSort()
    {
        if ($this->newSortValue != $this->oldSortValue) {
            if ($this->newSortValue < $this->oldSortValue) {
                $this->generateSort();
            } else {
                $queryGet = $this->select(
                    'id',
                    'sequence'
                )
                ->where('country_code', $this->country_code)
                ->where('language_code', $this->language_code)
                ->where('id', '!=', $this->id)
                ->where(function($queryGet2) {
                    $queryGet2
                        ->where('sequence', '<=', $this->newSortValue)
                        ->where('sequence', '>=', $this->oldSortValue);
                })
                ->orderBy('sequence')
                ->get()
                ->toArray();

                $sequence = $this->oldSortValue;

                foreach ($queryGet as $val) {
                    $this
                        ->where('id', $val['id'])
                        ->update([
                            'sequence' => $sequence
                        ]);

                    $sequence++;
                }
            }
        }

        return $this;
    }

    protected function resetSort()
    {
        $queryGet = $this->select(
            'id',
            'sequence'
        )
        ->where('country_code', $this->country_code)
        ->where('language_code', $this->language_code)
        ->orderBy('sequence')
        ->get()
        ->toArray();

        $sequence = 1;

        foreach ($queryGet as $val) {
            $this
                ->where('id', $val['id'])
                ->update([
                    'sequence' => $sequence
                ]);

            $sequence++;
        }
    }

    public function setTitleAttribute($title){
        $this->attributes['title'] = $title;

        $helper = new Helper();

        $this->attributes['slug'] = $helper->generateSlug($title);
    }


    public function productTagId()
    {
        return $this->hasOne(ProductTagId::class,'id','id_tag');
    }
}
