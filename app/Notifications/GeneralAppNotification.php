<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // 🟢 تم إضافة استدعاء واجهة الطوابير
use Illuminate\Notifications\Notification;

// 🟢 تم إضافة implements ShouldQueue عشان يشتغل في الخلفية
class GeneralAppNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $title;
    public $body;
    public $type; // task, chat, system
    public $url;

    // استقبال البيانات من الكنترولر
    public function __construct($title, $body, $type, $url)
    {
        $this->title = $title;
        $this->body = $body;
        $this->type = $type;
        $this->url = $url;
    }

    // تحديد طريقة الإرسال (هنكتفي بقاعدة البيانات حالياً)
    public function via($notifiable)
    {
        return ['database']; 
    }

    // تجهيز الداتا اللي هتتخزن في الداتا بيز (وتظهر في الجرس)
    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'type' => $this->type,
            'url' => $this->url,
        ];
    }
}