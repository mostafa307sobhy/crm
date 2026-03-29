<?php

namespace App\Traits;

use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait LogsActivity
{
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            self::recordLog('ADD', 'إضافة', $model);
        });

        static::updated(function ($model) {
            self::recordLog('UPDATE', 'تعديل', $model);
        });

        static::deleted(function ($model) {
            self::recordLog('DELETE', 'حذف', $model);
        });
    }

    protected static function recordLog($actionKey, $actionText, $model)
    {
        if (Auth::check()) {
            $modelName = class_basename($model);
            
            $dictionary = [
                'Client' => 'عميل',
                'Task' => 'مهمة',
                'Operation' => 'رسالة/إجراء',
                'Note' => 'ملاحظة',
                'Contact' => 'جهة اتصال',
                'Attachment' => 'مرفق',
                'User' => 'موظف',
            ];

            $arabicName = $dictionary[$modelName] ?? $modelName;
            $itemName = $model->name ?? $model->username ?? $model->task_desc ?? ('رقم تعريفي: ' . $model->id);

            // 🟢 استخدام أسماء الأعمدة الصحيحة الخاصة بك 🟢
            SystemLog::create([
                'user_id' => Auth::id(),
                'action_type' => $actionKey . ' ' . $modelName, // عشان الألوان تشتغل
                'action_details' => "قام (" . Auth::user()->username . ") بـ " . $actionText . " " . $arabicName . " ➜ " . Str::limit(strip_tags($itemName), 60),
                'ip_address' => request()->ip(), // تسجيل الأي بي
            ]);
        }
    }
}