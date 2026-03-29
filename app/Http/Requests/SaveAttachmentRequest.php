<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveAttachmentRequest extends FormRequest
{
    /**
     * تحديد الصلاحيات: مسموح للإدارة فقط بإضافة أو تعديل الوثائق
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * قواعد التحقق
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'drive_url' => 'required|url', // لازم يكون رابط صحيح
        ];
    }

    /**
     * رسائل الخطأ بالعربي
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم الوثيقة مطلوب .',
            'drive_url.required' => 'رابط الـ Drive مطلوب.',
            'drive_url.url' => 'لازم تدخل رابط (URL) صحيح.',
        ];
    }
}