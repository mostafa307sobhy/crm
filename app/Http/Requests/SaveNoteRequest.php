<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveNoteRequest extends FormRequest
{
    /**
     * تحديد الصلاحيات
     */
    public function authorize(): bool
    {
        // مسموح للأدمن فقط بإضافة أو تعديل الملاحظات
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * قواعد التحقق
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:note,alert',
            'content' => 'required|string',
        ];
    }

    /**
     * رسائل الخطأ المخصصة بالعربي
     */
    public function messages(): array
    {
        return [
            'type.required' => 'نوع الملاحظة مطلوب .',
            'type.in' => 'نوع الملاحظة غير صحيح.',
            'content.required' => 'محتوى الملاحظة مطلوب .',
        ];
    }
}