<?php

namespace App\Http\Controllers;

use App\Notifications\GeneralAppNotification;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // إضافة مهمة
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'task_desc' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'deadline' => 'required|date',
            'assigned_to' => 'required|array',
            'recurrence_type' => 'nullable|string',
            'attachment_url' => 'nullable|url',
            'recurrence_end_date' => 'nullable|date'
        ]);

        $task = \App\Models\Task::create([
            'client_id' => $request->client_id,
            'task_desc' => $request->task_desc,
            'priority' => $request->priority,
            'deadline' => $request->deadline,
            'attachment_url' => $request->attachment_url,
            'recurrence_type' => $request->recurrence_type ?? 'none',
            'recurrence_end_date' => $request->recurrence_end_date,
            'created_by' => auth()->id(),
            'status' => 'pending',
        ]);

        $task->assignedUsers()->attach($request->assigned_to);

        // رسالة تلقائية في الشات عند إنشاء مهمة جديدة
        $client = \App\Models\Client::find($request->client_id);
        $client->operations()->create([
            'user_id' => auth()->id(),
            'action_text' => '📌 تم تكليف مهمة جديدة: ' . $request->task_desc,
            'is_system' => true
        ]);

        // إرسال الإشعار للموظفين المكلفين بالمهمة
        if ($request->has('assigned_to')) {
            $assignedUsers = \App\Models\User::whereIn('id', $request->assigned_to)
                                ->where('id', '!=', auth()->id())
                                ->get();

            foreach ($assignedUsers as $notifiedUser) {
                $notifiedUser->notify(new GeneralAppNotification(
                    'تكليف جديد 📌',
                    'تم تكليفك بمهمة: ' . \Illuminate\Support\Str::limit($task->task_desc, 40),
                    'task',
                    route('clients.show', $task->client_id) . '#tasks'
                ));
            }
        }

        return back()->with('success', 'تم تكليف المهمة بنجاح!');
    }

    // تعديل بيانات المهمة بالكامل
    public function update(Request $request, \App\Models\Task $task)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'task_desc' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'deadline' => 'required|date',
            'assigned_to' => 'required|array',
            'recurrence_type' => 'nullable|string',
            'attachment_url' => 'nullable|url',
            'recurrence_end_date' => 'nullable|date'
        ]);

        $task->update([
            'client_id' => $request->client_id,
            'task_desc' => $request->task_desc,
            'priority' => $request->priority,
            'deadline' => $request->deadline,
            'attachment_url' => $request->attachment_url,
            'recurrence_type' => $request->recurrence_type ?? 'none',
            'recurrence_end_date' => $request->recurrence_end_date,
        ]);

        $task->assignedUsers()->sync($request->assigned_to);

        return back()->with('success', 'تم تعديل المهمة بنجاح!');
    }

    // إنجاز المهمة
    public function complete(Request $request, \App\Models\Task $task)
    {
        // 🟢 حل مشكلة N+1 Query التي أشار إليها المراجع بتسبيق جلب العلاقات 🟢
        $task->load(['client', 'assignedUsers']);

        $request->validate([
            'completion_reply' => 'nullable|string'
        ]);

        // 1. تغيير حالة المهمة الحالية إلى منجزة وتسجيل الرد
        $task->update([
            'status' => 'completed', 
            'completion_reply' => $request->completion_reply,
            'completed_by' => Auth::id(),
            'completed_at' => now()
        ]);

        // 2. إرسال الرسالة التلقائية في الشات
        $systemMessage = '✅ تم إنجاز المهمة: ' . $task->task_desc;
        if ($request->filled('completion_reply')) {
            $systemMessage .= '<br><strong class="text-primary mt-1 d-block"><i class="fas fa-comment-dots"></i> تقرير الإنجاز:</strong> ' . e($request->completion_reply);
        }

        $task->client->operations()->create([
            'user_id' => auth()->id(),
            'action_text' => $systemMessage,
            'is_system' => true
        ]);

        // 3. التوليد الفوري للمهمة الدورية
        if ($task->recurrence_type !== 'none') {
            if (!$task->recurrence_end_date || \Carbon\Carbon::parse($task->recurrence_end_date)->endOfDay()->isFuture()) {
                
                $newDeadline = \Carbon\Carbon::parse($task->deadline);
                if ($task->recurrence_type == 'daily') $newDeadline->addDay();
                elseif ($task->recurrence_type == 'weekly') $newDeadline->addWeek();
                elseif ($task->recurrence_type == 'monthly') $newDeadline->addMonth();

                $exists = \App\Models\Task::where('client_id', $task->client_id)
                    ->where('task_desc', $task->task_desc)
                    ->whereDate('deadline', $newDeadline->toDateString())
                    ->exists();

                if (!$exists) {
                    $newTask = $task->replicate();
                    $newTask->deadline = $newDeadline;
                    $newTask->status = 'pending';
                    $newTask->completion_reply = null; 
                    $newTask->completed_at = null;
                    $newTask->completed_by = null; 
                    $newTask->created_at = now();
                    $newTask->updated_at = now();
                    $newTask->save();

                    if ($task->assignedUsers->count() > 0) {
                        $newTask->assignedUsers()->attach($task->assignedUsers->pluck('id'));
                    }
                }
            }
        }
        
        // إرسال إشعار لصاحب المهمة
        if ($task->created_by && $task->created_by !== auth()->id()) {
            $creator = \App\Models\User::find($task->created_by);
            if ($creator) {
                $creator->notify(new \App\Notifications\GeneralAppNotification(
                    'تم إنجاز مهمة ✅',
                    'قام (' . auth()->user()->username . ') بإنجاز المهمة: ' . \Illuminate\Support\Str::limit($task->task_desc, 30),
                    'task',
                    route('clients.show', $task->client_id) . '#tasks'
                ));
            }
        }

        return back()->with('success', 'تم إنجاز المهمة وتسجيل تقرير العمل بنجاح! 👏');
    }

    // التراجع عن الإنجاز
    public function undo(\App\Models\Task $task)
    {
        // 🟢 حل مشكلة N+1 Query 🟢
        $task->load('assignedUsers');

        $task->update([
            'status' => 'pending', 
            'completed_at' => null,
            'completed_by' => null,
            'completion_reply' => null
        ]);
        
        // إرسال إشعار للموظفين
        foreach ($task->assignedUsers as $notifiedUser) {
            if ($notifiedUser->id !== auth()->id()) {
                $notifiedUser->notify(new GeneralAppNotification(
                    'إعادة فتح المهمة 🔄',
                    'تم التراجع عن إنجاز المهمة وإعادتها لقائمة المهام المعلقة: ' . \Illuminate\Support\Str::limit($task->task_desc, 30),
                    'task',
                    route('clients.show', $task->client_id) . '#tasks'
                ));
            }
        }
        
        return back()->with('success', 'تم استرجاع المهمة لقائمة المهام المعلقة.');
    }

    // حذف المهمة
    public function destroy(\App\Models\Task $task)
    {
        $task->delete();
        return back()->with('success', 'تم حذف المهمة بنجاح.');
    }
}