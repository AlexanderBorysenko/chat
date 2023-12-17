<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'content',
        'read',
        'type',
    ];

    // cast created_at to datetime
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
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
