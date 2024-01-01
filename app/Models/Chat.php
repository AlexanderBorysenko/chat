<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $appends = ['chat_name', 'unread_messages_count'];
    protected $guarded = [];

    protected $with = ['users'];

    // messages
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // users 
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function getUnreadMessagesCountAttribute()
    {
        return $this->messages()->where('sender_id', '!=', auth()->id())->where('read', false)->count();
    }

    public function getChatNameAttribute()
    {
        // return string from users names except auth user
        return $this->users()->where('user_id', '!=', auth()->id())->get()->pluck('name')->join(', ');
    }
}
