<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class ClientNote extends Model
{
        use  LogsActivity;

    use HasFactory;

    protected $fillable = ['client_id', 'user_id', 'type', 'content'];

    // العلاقة السحرية اللي الكنترولر بيدور عليها عشان يجيب اسم اللي كتب الملاحظة
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // (اختياري) علاقة الملاحظة بالعميل عشان نقفل الدائرة
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}