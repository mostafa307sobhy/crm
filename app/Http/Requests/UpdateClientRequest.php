<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        // مسموح للأدمن فقط بالتعديل
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'package_type' => 'required|string',
            'subscription_amount' => 'nullable|numeric|min:0',
            'visibility' => 'required|in:all,specific,admins_only',
            'sub_start_date' => 'nullable|date',
            'subscription_duration' => 'nullable|string',
            'tax_number' => 'nullable|string|max:255',
            'commercial_register' => 'nullable|string|max:255',
            'assigned_users' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم العميل مطلوب',
            'status.required' => 'حالة العميل مطلوبة',
            'package_type.required' => 'نوع الباقة مطلوب',
            'subscription_amount.numeric' => 'قيمة الاشتراك يجب أن تكون رقم',
            'subscription_amount.min' => 'قيمة الاشتراك لا يمكن أن تكون سالبة',
        ];
    }
}