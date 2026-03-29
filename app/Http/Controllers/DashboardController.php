<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{

public function index(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->role === 'admin';

        // 1. الإحصائيات الذكية (الأدمن يرى كل شيء، الموظف يرى عمله فقط)
        if ($isAdmin) {
            $activeClientsCount = \App\Models\Client::where('status', 'active')->count();
            $totalTasksCount = \App\Models\Task::count();
            $pendingTasksCount = \App\Models\Task::where('status', 'pending')->count();
            $completedTasksCount = \App\Models\Task::where('status', 'completed')->count();
        } else {
            // إحصائيات الموظف (المهام والعملاء المرتبطين به فقط)
            $activeClientsCount = \App\Models\Client::where('status', 'active')->whereHas('assignedUsers', function($q) use ($user) { $q->where('users.id', $user->id); })->count();
            $totalTasksCount = \App\Models\Task::whereHas('assignedUsers', function($q) use ($user) { $q->where('users.id', $user->id); })->count();
            $pendingTasksCount = \App\Models\Task::where('status', 'pending')->whereHas('assignedUsers', function($q) use ($user) { $q->where('users.id', $user->id); })->count();
            $completedTasksCount = \App\Models\Task::where('status', 'completed')->whereHas('assignedUsers', function($q) use ($user) { $q->where('users.id', $user->id); })->count();
        }

        // 2. بناء استعلام المهام
        $query = \App\Models\Task::with(['client', 'assignedUsers']);

        // تقييد المهام للموظف العادي ليرى مهامه فقط
        if (!$isAdmin) {
            $query->whereHas('assignedUsers', function($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        // --- تطبيق الفلاتر ---
        // فلتر البحث النصي (الجديد)
        if ($request->filled('search_text')) {
            $query->where('task_desc', 'like', '%' . $request->search_text . '%');
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        
        // فلتر المكلف (يظهر ويطبق للأدمن فقط)
        if ($isAdmin && $request->filled('user_id')) {
            $query->whereHas('assignedUsers', function($q) use ($request) {
                $q->where('users.id', $request->user_id);
            });
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('deadline', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('deadline', '<=', $request->date_to);
        }

        // 3. جلب البيانات مع الترقيم (10 مهام في كل صفحة)
        $pendingTasksList = (clone $query)->where('status', 'pending')->orderBy('deadline', 'asc')->paginate(10, ['*'], 'pending_page');
        $completedTasksList = (clone $query)->where('status', 'completed')->orderBy('completed_at', 'desc')->paginate(10, ['*'], 'completed_page');

        // 4. القوائم المنسدلة للفلتر
        if ($isAdmin) {
            $clients = \App\Models\Client::all();
            $users = \App\Models\User::all();
        } else {
            $clients = \App\Models\Client::whereHas('assignedUsers', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })->get();
            $users = collect([]); // الموظف العادي لا يحتاج فلتر الموظفين
        }

        return view('dashboard', compact(
            'activeClientsCount', 'totalTasksCount', 'pendingTasksCount', 'completedTasksCount',
            'pendingTasksList', 'completedTasksList', 'clients', 'users', 'isAdmin'
        ));
    }
}