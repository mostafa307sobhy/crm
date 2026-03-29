<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemLog;
// 🟢 المكتبات الجديدة المطلوبة لحماية تسجيل الدخول 🟢
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // عرض صفحة تسجيل الدخول
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // معالجة بيانات الدخول
    public function login(Request $request)
    {
        // التحقق من المدخلات
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // 🟢 1. إنشاء مفتاح فريد يعتمد على اسم المستخدم و IP الجهاز
        $throttleKey = Str::lower($request->input('username')) . '|' . $request->ip();

        // 🟢 2. فحص عدد المحاولات (الحد الأقصى 5 محاولات)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'username' => ["تم حظر الدخول مؤقتاً بسبب كثرة المحاولات الخاطئة. برجاء المحاولة بعد {$seconds} ثانية."],
            ]);
        }

        // محاولة تسجيل الدخول
        if (Auth::attempt($credentials)) {
            
            // 🟢 3. مسح سجل المحاولات الخاطئة بعد النجاح
            RateLimiter::clear($throttleKey);
            
            $request->session()->regenerate();
            
            // تسجيل حركة الدخول في الرقابة (System Logs)
            $roleName = auth()->user()->role === 'admin' ? 'المدير' : 'الموظف';
            SystemLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'LOGIN',
                'action_details' => "قام $roleName (" . auth()->user()->username . ") بتسجيل الدخول إلى النظام بنجاح.",
                'ip_address' => $request->ip()
            ]);
            
            return redirect()->intended('dashboard'); // التوجيه للوحة التحكم
        }

        // 🟢 4. تسجيل محاولة خاطئة في حالة الفشل
        RateLimiter::hit($throttleKey);

        // في حال خطأ بكلمة المرور أو اسم المستخدم
        return back()->withErrors([
            'username' => 'اسم المستخدم أو كلمة المرور غير صحيحة.',
        ])->onlyInput('username');
    }

    // تسجيل الخروج
    public function logout(Request $request)
    {
        // تسجيل حركة الخروج في الرقابة قبل إنهاء الجلسة
        if (auth()->check()) {
            $roleName = auth()->user()->role === 'admin' ? 'المدير' : 'الموظف';
            SystemLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'LOGOUT',
                'action_details' => "قام $roleName (" . auth()->user()->username . ") بتسجيل الخروج من النظام.",
                'ip_address' => $request->ip()
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}