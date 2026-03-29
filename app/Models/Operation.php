<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Operation extends Model
{
        use  LogsActivity;

    // ضفنا العواميد الجديدة هنا (الرابط والرد)
    protected $fillable = [
        'client_id', 'user_id', 'action_text', 'is_pinned', 
        'is_edited', 'is_system', 'attachment_url', 'reply_to_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // علاقة الإيموجي (الريأكتات)
    public function reactions()
    {
        return $this->hasMany(OperationReaction::class);
    }

    // علاقة الرد على رسالة معينة
    public function replyTo()
    {
        return $this->belongsTo(Operation::class, 'reply_to_id');
    }

    
}