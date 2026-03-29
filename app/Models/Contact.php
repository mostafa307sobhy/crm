<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class Contact extends Model
{
        use  LogsActivity;

    use HasFactory;

    // السطر السحري اللي بيسمح بحفظ البيانات
    protected $fillable = ['client_id', 'name', 'job_title', 'phone'];

    // (اختياري) علاقة جهة الاتصال بالعميل
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}