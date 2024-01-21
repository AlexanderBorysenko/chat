<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'sender_id',
        'content',
        'read',
        'type',
    ];

    // cast created_at to datetime
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }

    public function getContentAttribute($value)
    {
        try {
            return Crypt::decrypt($value);
        } catch (\Exception $e) {
            return 'Undecoded';
        }
    }

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = Crypt::encrypt($value);
    }
}
