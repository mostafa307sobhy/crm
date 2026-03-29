<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Document;
use App\Http\Requests\SaveAttachmentRequest; // 🟢 استدعاء ملف الـ Request الجديد

class AttachmentController extends Controller
{
    // إضافة وثيقة جديدة
    public function store(SaveAttachmentRequest $request, Client $client)
    {
        // تم نقل الـ Validation وفحص الـ Admin إلى ملف SaveAttachmentRequest
        
        $client->documents()->create($request->validated());

        return back()->with('success', 'تم ربط الوثيقة بنجاح! ✅');
    }

    // تعديل الوثيقة
    public function update(SaveAttachmentRequest $request, Document $attachment)
    {
        $attachment->update($request->validated());

        return back()->with('success', 'تم تعديل الوثيقة بنجاح ✅');
    }

    // حذف الوثيقة
    public function destroy(Document $attachment)
    {
        // حماية: الإدارة فقط هي اللي تحذف (لأن مفيش Form Request في الحذف بنعملها Inline)
        if (auth()->user()->role !== 'admin') abort(403);
        
        $attachment->delete();

        return back()->with('success', 'تم حذف الوثيقة بنجاح 🗑️');
    }
}