<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use App\Notifications\GeneralAppNotification; // 🟢 استدعاء كلاس الإشعارات

class OperationController extends Controller
{

    // إضافة تعليق أو عملية جديدة (مع دعم الروابط والردود والإشعارات)
    public function store(Request $request, Client $client)
    {
        // التحقق من البيانات المدخلة
        $request->validate([
            'action_text' => 'required|string|max:1000',
            'attachment_url' => 'nullable|url',
            'reply_to_id' => 'nullable|exists:operations,id'
        ]);

        // حفظ العملية في قاعدة البيانات وتخزينها في متغير
        $operation = $client->operations()->create([
            'user_id' => Auth::id(),
            'action_text' => $request->action_text,
            'attachment_url' => $request->attachment_url,
            'reply_to_id' => $request->reply_to_id,
        ]);

        // 🟢 إرسال إشعار لو الرسالة دي عبارة عن "رد" على زميل 🟢
        if ($request->filled('reply_to_id')) {
            $originalMessage = \App\Models\Operation::find($request->reply_to_id);
            
            // نتأكد إن الرسالة موجودة، وصاحبها مش هو نفس الشخص اللي بيرد
            if ($originalMessage && $originalMessage->user_id !== auth()->id() && $originalMessage->user) {
                $originalMessage->user->notify(new GeneralAppNotification(
                    'رد جديد في الشات 💬',
                    auth()->user()->username . ' قام بالرد على رسالتك.',
                    'chat',
                    route('clients.show', $client->id) . '#msg-' . $operation->id // الرابط هينزله على الرسالة علطول
                ));
            }
        }

        // العودة لصفحة العميل مع رسالة نجاح
        return back()->with('success', 'تمت إضافة التعليق بنجاح!');
    }

    // دالة تثبيت/إلغاء تثبيت الرسالة
    public function togglePin(\App\Models\Operation $operation)
    {
        // المديرين بس أو صاحب الرسالة هما اللي يقدروا يثبتوا
        if (auth()->user()->role !== 'admin' && auth()->id() !== $operation->user_id) {
            abort(403, 'غير مصرح لك بتثبيت هذه الرسالة.');
        }

        $operation->is_pinned = !$operation->is_pinned;
        $operation->save();

        return back()->with('success', $operation->is_pinned ? 'تم تثبيت الرسالة 📌' : 'تم إلغاء التثبيت.');
    }

    // تعديل الرسالة
    public function update(\Illuminate\Http\Request $request, \App\Models\Operation $operation)
    {
        if (auth()->user()->role !== 'admin' && auth()->id() !== $operation->user_id) {
            abort(403, 'غير مصرح لك بتعديل هذه الرسالة.');
        }

        $request->validate(['action_text' => 'required|string']);
        
        $operation->action_text = $request->action_text;
        $operation->is_edited = true; // نعلم إنها اتعدلت
        $operation->save();

        return back()->with('success', 'تم تعديل الرسالة بنجاح.');
    }

    // حذف الرسالة (للأدمن فقط بناءً على طلبك)
    public function destroy(\App\Models\Operation $operation)
    {
        // حماية قوية: الأدمن فقط هو اللي يمسح
        if (auth()->user()->role !== 'admin') {
            abort(403, 'للإدارة فقط صلاحية حذف الرسائل.');
        }

        $operation->delete();
        return back()->with('success', 'تم حذف الرسالة نهائياً.');
    }

    // تفاعلات الإيموجي (Slack Style)
    public function react(\Illuminate\Http\Request $request, \App\Models\Operation $operation)
    {
        $request->validate(['emoji' => 'required|string|in:👍,❤️,✅,😂,👀']);
        
        $userId = auth()->id();
        
        // لو الموظف داس على نفس الإيموجي تاني (بنمسحه)، لو إيموجي جديد (بنضيفه)
        $existingReaction = $operation->reactions()->where('user_id', $userId)->where('emoji', $request->emoji)->first();
        
        if ($existingReaction) {
            $existingReaction->delete(); // إلغاء التفاعل
        } else {
            $operation->reactions()->create([
                'user_id' => $userId,
                'emoji' => $request->emoji
            ]);
        }

        return back();
    }

    // مسح الشات بالكامل
    public function clearChat(\App\Models\Client $client)
    {
        if (auth()->user()->role !== 'admin') { abort(403); }
        $client->operations()->delete();
        return back()->with('success', 'تم مسح سجل المحادثة بالكامل بنجاح.');
    }

}