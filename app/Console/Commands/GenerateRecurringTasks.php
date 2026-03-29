<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Carbon\Carbon;

class GenerateRecurringTasks extends Command
{
    // اسم الأمر الذي سيعمل في الخلفية
    protected $signature = 'tasks:generate-recurring';
    protected $description = 'توليد المهام الدورية تلقائياً (يومي، أسبوعي، شهري)';

    public function handle()
    {
        $today = Carbon::today();

        // 1. جلب المهام المتكررة التي لم ينتهِ تاريخ إيقافها
        $tasks = Task::with('assignedUsers')
            ->where('recurrence_type', '!=', 'none')
            ->where(function($query) use ($today) {
                $query->whereNull('recurrence_end_date')
                      ->orWhereDate('recurrence_end_date', '>=', $today);
            })->get();

        foreach ($tasks as $task) {
            $deadline = Carbon::parse($task->deadline);
            $newDeadline = $deadline->copy();

            // 2. حساب الموعد الجديد بناءً على نوع التكرار
            if ($task->recurrence_type == 'daily') {
                $newDeadline->addDay();
            } elseif ($task->recurrence_type == 'weekly') {
                $newDeadline->addWeek();
            } elseif ($task->recurrence_type == 'monthly') {
                $newDeadline->addMonth();
            }

            // 3. إذا كان الموعد الجديد هو "اليوم" (أو مر عليه الوقت)
            if ($newDeadline->isToday() || $newDeadline->isPast()) {
                
                // التأكد من عدم وجود مهمة مطابقة لتجنب تكرار نفس المهمة مرتين
                $exists = Task::where('client_id', $task->client_id)
                    ->where('task_desc', $task->task_desc)
                    ->whereDate('deadline', $newDeadline->toDateString())
                    ->exists();

                if (!$exists) {
                    // توليد المهمة الجديدة
                    $newTask = $task->replicate();
                    $newTask->deadline = $newDeadline;
                    $newTask->status = 'pending';
                    $newTask->completed_at = null;
                    $newTask->created_at = now();
                    $newTask->updated_at = now();
                    $newTask->save();

                    // ربط الموظفين المكلفين بالمهمة الجديدة
                    if ($task->assignedUsers->count() > 0) {
                        $newTask->assignedUsers()->attach($task->assignedUsers->pluck('id'));
                    }
                }
            }
        }

        $this->info('تم مراجعة وتوليد المهام الدورية بنجاح!');
    }
}