<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientNote;
use App\Http\Requests\SaveNoteRequest; // 🟢 استدعاء ملف الـ Request الجديد

class NoteController extends Controller
{
    // إضافة ملاحظة جديدة
    public function store(SaveNoteRequest $request, Client $client)
    {
        // الـ SaveNoteRequest بيتأكد إن المستخدم مدير وبيفلتر البيانات
        // دمجنا الـ user_id مع البيانات المتفلترة عشان نعرف مين اللي كتب الملاحظة
        $client->notes()->create(array_merge(
            ['user_id' => auth()->id()],
            $request->validated()
        ));

        return back()->with('success', 'تم حفظ الملاحظة بنجاح! ✅');
    }

    // تعديل الملاحظة
    public function update(SaveNoteRequest $request, ClientNote $note)
    {
        $note->update($request->validated());

        return back()->with('success', 'تم تعديل الملاحظة بنجاح ✅');
    }

    // حذف الملاحظة
    public function destroy(ClientNote $note)
    {
        // حماية: الإدارة فقط هي اللي تحذف (بنعملها هنا عشان مفيش Request Class للحذف)
        if (auth()->user()->role !== 'admin') abort(403);
        
        $note->delete();

        return back()->with('success', 'تم حذف الملاحظة بنجاح 🗑️');
    }
}