<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Task;
use App\Models\User;
use App\Models\Operation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // 1. عرض لوحة التقارير الرئيسية (Dashboard)
    public function index(Request $request)
    {
        // 🟢 1. معالجة فلتر التواريخ (Date Filtering) 🟢
        $period = $request->input('period', 'all'); // الافتراضي: كل الأوقات
        
        $startDate = null;
        $endDate = now();

        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                break;
        }

        // دالة مساعدة لتطبيق الفلتر الزمني على أي كويري
        $applyDateFilter = function ($query, $column = 'created_at') use ($startDate, $endDate) {
            if ($startDate) {
                return $query->whereBetween($column, [$startDate, $endDate]);
            }
            return $query;
        };

        // 🟢 2. حساب متوسط سرعة الإنجاز (SLA - Average Resolution Time) 🟢
        // بنحسب الوقت بالساعات بين إنشاء المهمة وتحديثها لحالة (منجزة)
        $completedTasksForSLA = $applyDateFilter(Task::where('status', 'completed'), 'updated_at')->get();
        $avgResolutionHours = $completedTasksForSLA->count() > 0 
            ? round($completedTasksForSLA->avg(function($t) { return $t->created_at->diffInHours($t->updated_at); }), 1) 
            : 0;

        // 🟢 3. الإحصائيات الأساسية والمتقدمة (مع الفلتر) 🟢
        $stats = [
            'clients_total' => $applyDateFilter(Client::query())->count(),
            'clients_active' => $applyDateFilter(Client::where('status', 'active'))->count(),
            'users_total' => User::count(), // الموظفين مش بيتأثروا بالفلتر
            'tasks_total' => $applyDateFilter(Task::query())->count(),
            'tasks_pending' => $applyDateFilter(Task::where('status', 'pending'))->count(),
            'tasks_completed' => $applyDateFilter(Task::where('status', 'completed'))->count(),
            
            // 🚨 رادار التأخيرات (المهام المعلقة التي تخطت تاريخ التسليم)
            'tasks_overdue' => $applyDateFilter(Task::where('status', 'pending')->where('deadline', '<', now()))->count(),

            // 💰 الجزء المالي (Financial Metrics)
            'total_revenue' => $applyDateFilter(Client::where('status', 'active'))->sum('subscription_amount'),
            
            'expiring_subscriptions' => Client::where('status', 'active')
                ->whereNotNull('sub_end_date')
                ->whereBetween('sub_end_date', [now(), now()->addDays(7)])
                ->count(),

            // ⏱️ متوسط سرعة الإنجاز
            'avg_resolution_hours' => $avgResolutionHours,
        ];

        // 🟢 4. تقرير أعمار المهام المعلقة (Task Aging) ⏳
        // بنحسب المهام المعلقة بقالها قد إيه متعطلة (مهم جداً للمدير)
        $now = now();
        $aging = [
            'new' => Task::where('status', 'pending')->whereBetween('created_at', [$now->copy()->subDays(3), $now])->count(), // 1 لـ 3 أيام
            'warning' => Task::where('status', 'pending')->whereBetween('created_at', [$now->copy()->subDays(7), $now->copy()->subDays(3)->subSecond()])->count(), // 4 لـ 7 أيام
            'danger' => Task::where('status', 'pending')->where('created_at', '<', $now->copy()->subDays(7))->count(), // أكتر من 7 أيام (متعفنة)
        ];

        // 🟢 5. توقعات التدفق النقدي (Cash Flow Forecast) 💸
        // بنجيب أقرب عملاء اشتراكهم هيخلص عشان المبيعات تكلمهم يجددوا
        $cashFlowClients = Client::where('status', 'active')
            ->whereNotNull('sub_end_date')
            ->whereBetween('sub_end_date', [now(), now()->addDays(7)])
            ->orderBy('sub_end_date', 'asc')
            ->take(10)
            ->get();
        // إجمالي المبالغ المتوقع تحصيلها
        $stats['expected_cash_flow_total'] = $cashFlowClients->sum('subscription_amount');

        // 🟢 6. سجل أحدث النشاطات (مربوط بالفلتر) 🟢
        $recentOperations = $applyDateFilter(Operation::with(['user', 'client']))
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        // 🟢 7. بيانات الرسوم البيانية (المهام لكل عميل) 🟢
        $clientsWithTasks = $applyDateFilter(Client::withCount('tasks'))->having('tasks_count', '>', 0)->get();
        $tc_labels = $clientsWithTasks->pluck('name')->toArray();
        $tc_counts = $clientsWithTasks->pluck('tasks_count')->toArray();

        // 🟢 8. بيانات تقييم الموظفين (أكثر موظف أنجز مهام) 🟢
        $topEmployees = User::where('role', '!=', 'admin')
            ->withCount(['tasks as completed_tasks_count' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'completed');
                if ($startDate) {
                    $query->whereBetween('updated_at', [$startDate, $endDate]);
                }
            }])
            ->orderBy('completed_tasks_count', 'desc')
            ->take(5)
            ->get();
            
        $emp_labels = $topEmployees->pluck('username')->toArray();
        $emp_counts = $topEmployees->pluck('completed_tasks_count')->toArray();

        // 🟢 9. الخط الزمني للإنتاجية (المهام المنجزة يوم بيوم) 📈
        $productivityData = Task::where('status', 'completed')
            ->select(DB::raw('DATE(updated_at) as date'), DB::raw('count(*) as total'))
            ->when($startDate, function($query) use ($startDate, $endDate) {
                return $query->whereBetween('updated_at', [$startDate, $endDate]);
            })
            ->groupBy('date')->orderBy('date', 'asc')->get();
            
        $prod_labels = $productivityData->pluck('date')->map(fn($d) => Carbon::parse($d)->format('m-d'))->toArray();
        $prod_counts = $productivityData->pluck('total')->toArray();

        // 🟢 10. خريطة حِمل العمل (Workload Distribution) ⚖️
        // بتجيب المهام المعلقة مع كل موظف عشان الإدارة توزع الشغل صح
        $workloadEmployees = User::where('role', '!=', 'admin')
            ->withCount(['tasks as pending_tasks_count' => function ($query) {
                $query->where('status', 'pending');
            }])
            ->orderBy('pending_tasks_count', 'desc')
            ->get();
        
        $wl_labels = $workloadEmployees->pluck('username')->toArray();
        $wl_counts = $workloadEmployees->pluck('pending_tasks_count')->toArray();

        // 🟢 11. قائمة عملاء الـ VIP (أكثر عملاء استهلاكاً للمهام) 👑
        $topClients = Client::withCount(['tasks' => function($q) use ($startDate, $endDate) {
                if ($startDate) $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->having('tasks_count', '>', 0)
            ->orderBy('tasks_count', 'desc')
            ->take(5)
            ->get();

        // 🟢 12. بيانات التقويم (المهام المعلقة) 🟢
        $tasks = Task::with('client')->where('status', 'pending')->get();
        $events = [];
        foreach ($tasks as $task) {
            $color = '#0dcaf0'; 
            if ($task->priority == 'high') $color = '#dc3545'; 
            elseif ($task->priority == 'medium') $color = '#ffc107'; 

            $clientName = $task->client ? $task->client->name . ' - ' : '';
            
            $events[] = [
                'title' => $clientName . mb_substr($task->task_desc, 0, 30, 'UTF-8') . '...',
                'start' => Carbon::parse($task->deadline)->format('Y-m-d\TH:i:s'),
                'backgroundColor' => $color,
                'borderColor' => 'transparent',
                'extendedProps' => [
                    'desc' => $task->task_desc
                ]
            ];
        }

        // 🟢 الإرجاع عبر الـ AJAX (للتحديث الحي بدون ريفرش) 🟢
        if ($request->ajax()) {
            return response()->json([
                'stats' => $stats,
                'top_clients' => $topClients,
                'aging' => $aging,
                'cash_flow_clients' => $cashFlowClients,
                'chart_data' => [
                    'clients' => ['labels' => $tc_labels, 'data' => $tc_counts],
                    'employees' => ['labels' => $emp_labels, 'data' => $emp_counts],
                    'productivity' => ['labels' => $prod_labels, 'data' => $prod_counts],
                    'workload' => ['labels' => $wl_labels, 'data' => $wl_counts]
                ]
            ]);
        }

        // لو طلب عادي (أول مرة يفتح الصفحة)
        return view('reports.index', compact(
            'stats', 'recentOperations', 'tc_labels', 'tc_counts', 
            'events', 'emp_labels', 'emp_counts', 'prod_labels', 'prod_counts', 
            'topClients', 'wl_labels', 'wl_counts', 'aging', 'cashFlowClients', 'period'
        ));
    }

    // =========================================================================
    // 2. سجل المهام المنجزة (مخصصة للإدارة لمراجعة شغل الموظفين)
    // =========================================================================
    public function completedTasks(Request $request)
    {
        $query = Task::with(['client', 'assignedUsers']) 
                     ->where('status', 'completed')
                     ->orderBy('updated_at', 'desc');

        if ($request->has('user_id') && $request->user_id != '') {
            $query->whereHas('assignedUsers', function($q) use ($request) {
                $q->where('users.id', $request->user_id);
            });
        }

        if ($request->has('client_id') && $request->client_id != '') {
            $query->where('client_id', $request->client_id);
        }

        $completedTasks = $query->paginate(20);
        $users = User::where('role', '!=', 'admin')->get();
        $clients = Client::orderBy('name', 'asc')->get(); 

        return view('reports.completed_tasks', compact('completedTasks', 'users', 'clients'));
    }

    // =========================================================================
    // 3. سجل المهام المعلقة والمتأخرة (تفاصيل الكروت)
    // =========================================================================
    public function pendingTasks(Request $request)
    {
        $query = Task::with(['client', 'assignedUsers'])
                     ->where('status', 'pending')
                     ->orderBy('deadline', 'asc'); // ترتيب بالأقرب تسليماً

        // لو جينا من كارت "المهام المتأخرة" (رادار التأخيرات)
        $isOverdue = $request->has('filter') && $request->filter == 'overdue';
        if ($isOverdue) {
            $query->where('deadline', '<', now());
        }

        // فلاتر البحث العادية
        if ($request->has('user_id') && $request->user_id != '') {
            $query->whereHas('assignedUsers', function($q) use ($request) {
                $q->where('users.id', $request->user_id);
            });
        }
        if ($request->has('client_id') && $request->client_id != '') {
            $query->where('client_id', $request->client_id);
        }

        $pendingTasks = $query->paginate(20);
        $users = User::where('role', '!=', 'admin')->get();
        $clients = Client::orderBy('name', 'asc')->get();

        return view('reports.pending_tasks', compact('pendingTasks', 'users', 'clients', 'isOverdue'));
    }
}