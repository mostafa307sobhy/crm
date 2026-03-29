<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SystemLogController extends Controller
{

    public function index(\Illuminate\Http\Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'غير مصرح لك بالوصول لسجل الرقابة.');
        }

        // 1. التقاط كلمة البحث من الرابط (إن وجدت)
        $searchTerm = $request->search;

        // 2. بناء الاستعلام
        $query = \App\Models\SystemLog::with('user')->orderBy('created_at', 'desc');

        // 3. إذا كان هناك كلمة بحث، فلتر السجلات بناءً عليها
        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('action_type', 'like', "%{$searchTerm}%")
                  ->orWhere('action_details', 'like', "%{$searchTerm}%")
                  ->orWhere('ip_address', 'like', "%{$searchTerm}%")
                  // البحث باسم الموظف المرتبط بالعملية
                  ->orWhereHas('user', function($userQuery) use ($searchTerm) {
                      $userQuery->where('username', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // 4. تقسيم النتائج (10 في الصفحة) وإرسالها
        $logs = $query->paginate(10);

        return view('system_logs.index', compact('logs'));
    }
}