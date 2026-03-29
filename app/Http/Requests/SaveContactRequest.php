<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveContactRequest extends FormRequest
{
    /**
     * تحديد الصلاحيات
     */
    public function authorize(): bool
    {
        // مسموح للكل بالإضافة (لأن الكنترولر نفسه بيحمي عمليات التعديل والحذف للأدمن فقط)
        return true; 
    }

    /**
     * قواعد التحقق
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ];
    }

    /**
     * رسائل الخطأ المخصصة بالعربي
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم جهة الاتصال مطلوب .',
            'name.max' => 'الاسم طويل جداً (الحد الأقصى 255 حرف).',
            'job_title.max' => 'المسمى الوظيفي طويل جداً.',
            'phone.max' => 'رقم الهاتف طويل جداً.',
        ];
    }
}