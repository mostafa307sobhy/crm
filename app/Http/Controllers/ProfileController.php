<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;



class ProfileController extends Controller
{

// عرض صفحة الإعدادات
    public function index()
    {
        return view('profile.index');
    }

    // تحديث اسم المستخدم
    public function updateUsername(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:100|unique:users,username,' . Auth::id(),
        ], [
            'username.unique' => 'اسم المستخدم هذا مأخوذ مسبقاً، يرجى اختيار اسم آخر.'
        ]);

        $user = Auth::user();
        $oldName = $user->username;
        
        $user->update(['username' => $request->username]);

        // تسجيل الحركة في الرقابة

        return back()->with('success', 'تم تحديث اسم المستخدم بنجاح!');
    }

    // تحديث كلمة المرور
    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|string|min:6',
        ]);

        $user = Auth::user();

        // التأكد من أن الباسورد القديم صحيح
        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'كلمة المرور الحالية غير صحيحة.']);
        }

        // تشفير وحفظ الباسورد الجديد
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);


        return back()->with('success', 'تم تغيير كلمة المرور بنجاح وآمان!');
    }
}