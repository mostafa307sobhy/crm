<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, LogsActivity;

    protected $fillable = [
        'username',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // الموظف وعملائه المخصصين (نفس العلاقة، اسمان مختلفان للتوافق)
    public function assignedClients()
    {
        return $this->belongsToMany(Client::class, 'client_user', 'user_id', 'client_id');
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_user');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_user', 'user_id', 'task_id');
    }

    public function operations()
    {
        return $this->hasMany(Operation::class);
    }

    public function logs()
    {
        return $this->hasMany(SystemLog::class);
    }
}