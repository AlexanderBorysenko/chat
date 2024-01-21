<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'path',
        'url',
        'user_id',
    ];
    public $timestamps = false;

    protected static function booted()
    {
        static::deleting(function ($music) {
            \Storage::delete($music->path);
        });
    }
}
