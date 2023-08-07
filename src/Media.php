<?php


namespace Codelines\LaravelMediaController;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Media extends Model
{
    protected $appends = ['url'];
    protected $hidden = [
        'created_at',
        'updated_at',
        'object_type',
        'object_id',
        'orig_name',
        'fs',
        'path'
    ];
    public $incrementing = false;

    protected static function boot()
    {
        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->id = (string)Str::uuid();
            }
        });

        parent::boot();
    }

    public function getUrlAttribute()
    {
        return url(Storage::url($this->path));
    }
}
