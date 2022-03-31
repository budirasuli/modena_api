<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * App\Model\Media
 *
 * @property int $id
 * @property string $mediable_type
 * @property int $mediable_id
 * @property string|null $uuid
 * @property string|null $content_type
 * @property string $name
 * @property string $path
 * @property string $file_name
 * @property string $type
 * @property string|null $mime_type
 * @property string $extension
 * @property string $disk
 * @property float $size
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $directory
 * @property-read Model|\Eloquent $mediable
 * @method static \Illuminate\Database\Eloquent\Builder|Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Media query()
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereContentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereMediableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereMediableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereUuid($value)
 * @mixin \Eloquent
 */
class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'uuid', 'name', 'path', 'file_name', 'mime_type', 'disk', 'size', 'type', 'extension', 'content_type'
    ];

    protected $appends = ['directory'];

    /**
     * Get the owning mediable model.
     */
    public function mediable()
    {
        return $this->morphTo();
    }

    public function getPathAttribute($value)
    {
        return Storage::disk($this->attributes['disk'])
            ->url($this->attributes['path'] . '/' . $this->attributes['file_name']);
    }

    public function getDirectoryAttribute($value)
    {
        return $this->attributes['path'] . '/' . $this->attributes['file_name'];
    }
}
