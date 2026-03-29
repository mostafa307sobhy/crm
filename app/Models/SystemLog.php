<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{

    protected $guarded = [];

    // الموظف صاحب الحركة
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // دالة سحرية لتسجيل أي حركة في النظام بسطر واحد
    public static function record($type, $details)
    {
        self::create([
            'user_id' => auth()->id(),
            'action_type' => $type,
            'action_details' => $details,
            'ip_address' => request()->ip(), // تسجيل الـ IP تلقائياً
        ]);
    }
}
