<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        

        // السحر هنا: أضفنا with('assignedClients') لجلب بيانات العملاء الفعلية
        // وبقينا على withCount لجلب الإحصائيات (الأرقام) لكي يعمل النظام بأقصى سرعة
        $users = User::with('assignedClients')->withCount([
            'assignedClients', 
            'tasks as pending_tasks_count' => function($q) { $q->where('status', 'pending'); },
            'tasks as completed_tasks_count' => function($q) { $q->where('status', 'completed'); }
        ])->orderBy('created_at', 'desc')->get();

        // جلب كل العملاء النشطين لكي نستخدمهم في نافذة "ربط العملاء"
        $allClients = Client::where('status', 'active')->get();

        return view('users.index', compact('users', 'allClients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,user'
        ]);

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return back()->with('success', 'تم إضافة الموظف بنجاح.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|unique:users,username,' . $user->id,
            'role' => 'required|in:admin,user'
        ]);

        $user->username = $request->username;
        $user->role = $request->role;
        
        // تغيير الباسورد فقط إذا تم كتابة باسورد جديد
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        return back()->with('success', 'تم تحديث بيانات الموظف.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك حذف حسابك الشخصي!');
        }
        $user->delete();
        return back()->with('success', 'تم حذف الموظف بنجاح.');
    }

    // الدالة السحرية لربط الموظف بالعملاء بضغطة واحدة
    public function assignClients(Request $request, User $user)
    {
        // sync تقوم بمسح القديم وإضافة الجديد، أو تفريغهم لو لم يحدد شيئاً
        $user->assignedClients()->sync($request->clients ?? []);
        return back()->with('success', 'تم تحديث صلاحيات وصول الموظف للعملاء.');
    }
}