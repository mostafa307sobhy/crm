<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // جلب المهام من القاعدة القديمة
        $oldTasks = DB::select('SELECT * FROM u757178015_crm_db.tasks');
        
        foreach ($oldTasks as $task) {
            // إدخال المهمة في الجدول الجديد
            DB::table('tasks')->insert([
                'id' => $task->id,
                'client_id' => $task->client_id,
                'task_desc' => $task->task_desc,
                'request_date' => $task->request_date,
                'priority' => $task->priority,
                'recurrence' => $task->recurrence,
                'recurrence_end_date' => $task->recurrence_end_date,
                'created_by' => $task->created_by,
                'status' => $task->status,
                'completed_by' => $task->completed_by,
                'completed_at' => $task->completed_at,
                'deadline' => $task->deadline,
                'created_at' => $task->created_at,
                'updated_at' => $task->created_at, // نعتبر وقت التحديث هو نفسه وقت الإنشاء القديم
            ]);

            // تفكيك الموظفين الموكلين (assigned_to) وإضافتهم للجدول الوسيط
            if (!empty($task->assigned_to)) {
                $userIds = explode(',', $task->assigned_to);
                foreach ($userIds as $uid) {
                    if (trim($uid) !== '') {
                        DB::table('task_user')->insert([
                            'task_id' => $task->id,
                            'user_id' => trim($uid),
                        ]);
                    }
                }
            }
        }

        $this->command->info('تم نقل المهام وتوزيع الموظفين بنجاح! 📋');
    }
}