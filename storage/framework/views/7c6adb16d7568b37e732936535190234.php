 
<?php $__env->startSection('title', $isOverdue ? 'سجل المهام المتأخرة' : 'سجل المهام المعلقة'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0">
            <?php if($isOverdue): ?>
                <i class="fas fa-exclamation-triangle text-danger me-2 pulse-danger"></i> سجل المهام المتأخرة (SLA)
            <?php else: ?>
                <i class="fas fa-tasks text-warning me-2"></i> سجل المهام المعلقة
            <?php endif; ?>
        </h3>
        <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-secondary rounded-pill shadow-sm">
            <i class="fas fa-arrow-right me-1"></i> العودة للتقارير
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="<?php echo e(route('pending_tasks.index')); ?>" method="GET" class="row g-3 align-items-end">
                <?php if($isOverdue): ?> <input type="hidden" name="filter" value="overdue"> <?php endif; ?>
                
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">فلترة بالموظف</label>
                    <select name="user_id" class="form-select border-light shadow-sm">
                        <option value="">-- جميع الموظفين --</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>><?php echo e($user->username); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-muted">فلترة بالعميل</label>
                    <select name="client_id" class="form-select border-light shadow-sm">
                        <option value="">-- جميع العملاء --</option>
                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($client->id); ?>" <?php echo e(request('client_id') == $client->id ? 'selected' : ''); ?>><?php echo e($client->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm fw-bold"><i class="fas fa-filter me-1"></i> بحث</button>
                    <?php if(request()->has('user_id') || request()->has('client_id')): ?>
                        <a href="<?php echo e(route('pending_tasks.index', $isOverdue ? ['filter'=>'overdue'] : [])); ?>" class="btn btn-light border text-danger shadow-sm" title="إلغاء الفلاتر"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th class="py-3 px-4 text-start">تفاصيل المهمة</th>
                            <th class="py-3">العميل</th>
                            <th class="py-3">المكلفون</th>
                            <th class="py-3">الأولوية</th>
                            <th class="py-3">موعد التسليم (الديدلاين)</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php $__empty_1 = true; $__currentLoopData = $pendingTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php 
                                $isTaskOverdue = \Carbon\Carbon::parse($task->deadline)->isPast(); 
                            ?>
                            <tr>
                                <td class="px-4 text-start">
                                    <span class="fw-bold text-dark d-block mb-1"><?php echo e(\Illuminate\Support\Str::limit($task->task_desc, 60)); ?></span>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('clients.show', $task->client_id)); ?>" class="text-decoration-none fw-bold text-primary hover-zoom d-inline-block">
                                        <i class="fas fa-building me-1 opacity-50"></i> <?php echo e($task->client->name ?? 'عميل محذوف'); ?>

                                    </a>
                                </td>
                                <td>
                                    <?php $__currentLoopData = $task->assignedUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge bg-secondary mb-1"><i class="fas fa-user me-1 opacity-50"></i> <?php echo e($u->username); ?></span><br>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </td>
                                <td>
                                    <?php if($task->priority == 'high'): ?> <span class="badge bg-danger">عاجلة جداً</span>
                                    <?php elseif($task->priority == 'medium'): ?> <span class="badge bg-warning text-dark">متوسطة</span>
                                    <?php else: ?> <span class="badge bg-info text-dark">عادية</span> <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold <?php echo e($isTaskOverdue ? 'text-danger' : 'text-dark'); ?>">
                                        <i class="far fa-calendar-alt me-1"></i> <?php echo e(\Carbon\Carbon::parse($task->deadline)->format('Y-m-d')); ?><br>
                                        <small><i class="far fa-clock me-1 mt-1"></i> <?php echo e(\Carbon\Carbon::parse($task->deadline)->format('h:i A')); ?></small>
                                    </div>
                                    <?php if($isTaskOverdue): ?>
                                        <span class="badge bg-danger mt-1 blink-hard">متأخرة!</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="py-5 text-center text-muted">
                                    <i class="fas fa-check-double fa-3x mb-3 text-success opacity-50"></i>
                                    <h5>لا توجد مهام معلقة تطابق بحثك</h5>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php if($pendingTasks->hasPages()): ?>
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-center">
                    <?php echo e($pendingTasks->appends(request()->query())->links('pagination::bootstrap-5')); ?>

                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<style>
    .blink-hard { animation: blinker 1s linear infinite; }
    @keyframes blinker { 50% { opacity: 0; } }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\crm\accounting-crm\resources\views/reports/pending_tasks.blade.php ENDPATH**/ ?>