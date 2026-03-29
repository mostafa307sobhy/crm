<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;


class Client extends Model
{
    use SoftDeletes, LogsActivity;
    
    // 🟢 1. تم إضافة is_workspace و is_active هنا 🟢
    protected $fillable = [
        'name', 'status', 'package_type', 'subscription_amount', 
        'tax_number', 'commercial_register', 'sub_start_date', 
        'subscription_duration', 'sub_end_date', 'visibility',
        'is_workspace', 'is_active' 
    ];

    /**
     * تحويل البيانات تلقائياً للأنواع الصحيحة
     */
    protected $casts = [
        'sub_start_date' => 'date',
        'sub_end_date' => 'date',
        'subscription_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'is_workspace' => 'boolean', // 🟢 2. تم إضافة دي عشان يتعامل كـ True/False 🟢
    ];

    /**
     * المهام الخاصة بالعميل
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * سجل العمليات (Timeline)
     */
    public function operations()
    {
        return $this->hasMany(Operation::class);
    }

    /**
     * الموظفين المخصصين لهذا العميل
     */
    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'client_user');
    }

    /**
     * الوثائق المربوطة (روابط Drive)
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * جهات الاتصال
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * الملاحظات والتنبيهات
     */
    public function notes()
    {
        return $this->hasMany(ClientNote::class);
    }

    /**
     * حذف تلقائي للبيانات المرتبطة عند حذف العميل
     */
    protected static function booted()
    {
        static::deleting(function ($client) {
            $client->tasks()->delete();
            $client->notes()->delete();
            $client->contacts()->delete();
            $client->documents()->delete();
            $client->operations()->delete();
        });
    }
}