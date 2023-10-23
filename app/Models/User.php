<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];


    public function messages()
    {
        // select by sender_id and receiver_id
        return $this->hasMany(Message::class, 'sender_id')->orWhere('receiver_id', $this->id);
    }

    public function currentUserChat()
    {
        // return all messages where sender_id is current user or receiver_id is current user, order by created_at
        return $this->messages()->with('sender')->where('sender_id', auth()->id())->orWhere('receiver_id', auth()->id())->orderBy('created_at');
    }

    public function currentUserUnreadMessages()
    {
        return $this->hasMany(Message::class, 'sender_id')->where('receiver_id', auth()->id())->where('read', false);
    }
}
