<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contact;
use App\Http\Requests\SaveContactRequest; // 🟢 استدعاء ملف الـ Request الجديد

class ContactController extends Controller
{
    // إضافة جهة اتصال جديدة
    public function store(SaveContactRequest $request, Client $client)
    {
        // الداتا جاية متفلترة وجاهزة من الـ SaveContactRequest
        $client->contacts()->create($request->validated());
      
        return back()->with('success', 'تم إضافة جهة الاتصال بنجاح! ✅');
    }

    // تعديل جهة الاتصال
    public function update(SaveContactRequest $request, Contact $contact)
    {
        // حماية: الإدارة فقط هي اللي تعدل
        if (auth()->user()->role !== 'admin') abort(403);

        $contact->update($request->validated());
        
        return back()->with('success', 'تم تعديل جهة الاتصال بنجاح ✅');
    }

    // حذف جهة الاتصال
    public function destroy(Contact $contact)
    {
        // حماية: الإدارة فقط هي اللي تحذف
        if (auth()->user()->role !== 'admin') abort(403);
        
        $contact->delete();
      
        return back()->with('success', 'تم حذف جهة الاتصال بنجاح 🗑️');
    }
}