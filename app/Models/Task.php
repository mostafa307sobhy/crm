<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class Task extends Model
{
        use  LogsActivity;

    use SoftDeletes;
    
 protected $fillable = [
    'client_id', 'task_desc', 'priority', 'deadline', 'attachment_url', 
    'recurrence_type', 'recurrence_end_date', 'status', 
    'created_by', 'completed_by', 'completed_at', 'completion_reply'
];

    /**
     * تحويل البيانات تلقائياً للأنواع الصحيحة
     */
    protected $casts = [
        'deadline' => 'datetime',
        'completed_at' => 'datetime',
        'request_date' => 'datetime',
        'recurrence_end_date' => 'date',
    ];

    /**
     * العميل الخاص بالمهمة
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * الموظف الذي أنشأ المهمة
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * الموظف الذي أنجز المهمة
     */
    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * الموظفين المكلفين بالمهمة
     */
    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'task_user');
    }
}
