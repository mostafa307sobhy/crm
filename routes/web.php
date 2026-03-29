<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\SystemLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\IsAdmin; // استدعاء البودي جارد

// ==========================================
// 1. مسارات الزوار (تسجيل الدخول)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/', [AuthController::class, 'login'])->middleware('throttle:5,1');});

// ==========================================
// 2. مسارات النظام (لجميع الموظفين المصرح لهم)
// ==========================================
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/username', [ProfileController::class, 'updateUsername'])->name('profile.username');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::post('/operations/{operation}/pin', [App\Http\Controllers\OperationController::class, 'togglePin'])->name('operations.pin');

    Route::put('/operations/{operation}', [App\Http\Controllers\OperationController::class, 'update'])->name('operations.update');
    Route::delete('/operations/{operation}', [App\Http\Controllers\OperationController::class, 'destroy'])->name('operations.destroy');
    Route::post('/operations/{operation}/react', [App\Http\Controllers\OperationController::class, 'react'])->name('operations.react');

    // العملاء (عرض فقط للموظفين)
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('/clients/{client}/users', [ClientController::class, 'getAssignedUsers']);
    
    // المهام والعمليات (متاحة للموظفين)
    Route::post('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::post('/clients/{client}/operations', [OperationController::class, 'store'])->name('operations.store');
    Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');

    // ==========================================
    // مسارات الإشعارات الذكية
    // ==========================================
    Route::get('/notifications/fetch', [App\Http\Controllers\NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/read/{id}', [App\Http\Controllers\NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'destroy']);

    // 🟢 مسار فحص الإشعارات الحية (AJAX) اللي لسه عاملينه 🟢
    Route::get('/check-notifications', function () {
        return response()->json([
            'count' => auth()->user()->unreadNotifications->count()
        ]);
    })->name('notifications.check');


    // ==========================================
    // 3. مسارات الإدارة فقط (محمية بواسطة البودي جارد IsAdmin)
    // ==========================================
    Route::middleware(IsAdmin::class)->group(function () {
        
        // مسح الشات بالكامل للعميل (تم إضافته هنا للحماية)
        Route::delete('/clients/{client}/operations/clear', [OperationController::class, 'clearChat'])->name('operations.clear');

        // إدارة العملاء
        Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

        // إدارة المهام
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
        Route::post('/tasks/{task}/undo', [TaskController::class, 'undo'])->name('tasks.undo');

        // ملحقات العميل (ملاحظات، جهات اتصال، وثائق)
        Route::post('/clients/{client}/notes', [NoteController::class, 'store'])->name('notes.store');
        Route::post('/clients/{client}/contacts', [ContactController::class, 'store'])->name('contacts.store');
        Route::post('/clients/{client}/attachments', [AttachmentController::class, 'store'])->name('attachments.store');

        // التقارير والموظفين وسجل الرقابة
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/system-logs', [SystemLogController::class, 'index'])->name('system_logs.index');

        Route::get('/pending-tasks', [App\Http\Controllers\ReportController::class, 'pendingTasks'])->name('pending_tasks.index');

        
        Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
        Route::post('users/{user}/assign-clients', [UserController::class, 'assignClients'])->name('users.assign_clients');

        Route::get('/completed-tasks', [App\Http\Controllers\ReportController::class, 'completedTasks'])->name('completed_tasks.index');

        // مسارات تعديل وحذف الملاحظات
        Route::put('/notes/{note}', [App\Http\Controllers\NoteController::class, 'update'])->name('notes.update');
        Route::delete('/notes/{note}', [App\Http\Controllers\NoteController::class, 'destroy'])->name('notes.destroy');

        // مسارات تعديل وحذف جهات الاتصال
        Route::put('/contacts/{contact}', [App\Http\Controllers\ContactController::class, 'update'])->name('contacts.update');
        Route::delete('/contacts/{contact}', [App\Http\Controllers\ContactController::class, 'destroy'])->name('contacts.destroy');
    
        // مسارات تعديل وحذف الوثائق (Attachments)
        Route::put('/attachments/{attachment}', [App\Http\Controllers\AttachmentController::class, 'update'])->name('attachments.update');
        Route::delete('/attachments/{attachment}', [App\Http\Controllers\AttachmentController::class, 'destroy'])->name('attachments.destroy');
    
    });
});