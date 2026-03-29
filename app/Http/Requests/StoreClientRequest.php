<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    /**
     * تحديد من له صلاحية تنفيذ هذا الطلب
     */
    public function authorize(): bool
    {
        // مسموح للأدمن فقط بإضافة عميل
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * قواعد التحقق من البيانات
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'package_type' => 'required|string',
            'status' => 'required|in:active,inactive',
            'sub_start_date' => 'nullable|date',
            'subscription_duration' => 'nullable|string',
            'assigned_users' => 'nullable|array',
            'tax_number' => 'nullable|string|max:255',
            'commercial_register' => 'nullable|string|max:255',
            'subscription_amount' => 'nullable|numeric|min:0',
            'visibility' => 'required|in:all,specific,admins_only',
        ];
    }

    /**
     * رسائل الخطأ المخصصة باللغة العربية
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم العميل مطلوب',
            'name.max' => 'اسم العميل طويل جداً (الحد الأقصى 255 حرف)',
            'package_type.required' => 'نوع الباقة مطلوب',
            'status.required' => 'حالة العميل مطلوبة',
            'status.in' => 'حالة العميل غير صحيحة',
            'subscription_amount.numeric' => 'قيمة الاشتراك يجب أن تكون رقم',
            'subscription_amount.min' => 'قيمة الاشتراك لا يمكن أن تكون سالبة',
            'visibility.required' => 'نطاق الرؤية مطلوب',
            'visibility.in' => 'نطاق الرؤية غير صحيح',
        ];
    }
}