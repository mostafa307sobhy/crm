<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // جلب الإشعارات والأرقام اللحظية
    public function fetch()
    {
        $user = auth()->user();
        return response()->json([
            'unread_count' => $user->unreadNotifications->count(),
            'notifications' => $user->notifications()->take(50)->get() // هنجيب أحدث 50 إشعار
        ]);
    }

    // تحديد إشعار كمقروء
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();
        if ($notification) { $notification->markAsRead(); }
        return response()->json(['success' => true]);
    }

    // حذف الإشعار نهائياً (زر الـ X)
    public function destroy($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();
        if ($notification) { $notification->delete(); }
        return response()->json(['success' => true]);
    }

    // تحديد الكل كمقروء
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }
}