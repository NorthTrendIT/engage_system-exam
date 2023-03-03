<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConversationMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'message',
        'is_read',
        'sender_delete',
        'receiver_delete',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class,'conversation_id');
    }
}
