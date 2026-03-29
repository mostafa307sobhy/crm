 
<?php $__env->startSection('title', 'سجل المهام المنجزة'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0">
            <i class="fas fa-check-circle text-success me-2"></i> سجل المهام المنجزة
        </h3>
        <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-secondary rounded-pill shadow-sm">
            <i class="fas fa-arrow-right me-1"></i> العودة للتقارير
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="<?php echo e(route('completed_tasks.index')); ?>" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">فلترة بالموظف</label>
                    <select name="user_id" class="form-select border-light shadow-sm">
                        <option value="">-- جميع الموظفين --</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>>
                                <?php echo e($user->username); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-muted">فلترة بالعميل</label>
                    <select name="client_id" class="form-select border-light shadow-sm">
                        <option value="">-- جميع العملاء --</option>
                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($client->id); ?>" <?php echo e(request('client_id') == $client->id ? 'selected' : ''); ?>>
                                <?php echo e($client->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm fw-bold">
                        <i class="fas fa-filter me-1"></i> بحث
                    </button>
                    <?php if(request()->has('user_id') || request()->has('client_id')): ?>
                        <a href="<?php echo e(route('completed_tasks.index')); ?>" class="btn btn-light border text-danger shadow-sm" title="إلغاء الفلاتر">
                            <i class="fas fa-times"></i>
                        </a>
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
                            <th class="py-3">الموظف (المُنفذ)</th>
                            <th class="py-3">تاريخ الإنجاز</th>
                            <th class="py-3">تقرير العمل / الرد</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <?php $__empty_1 = true; $__currentLoopData = $completedTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-4 text-start">
                                    <span class="fw-bold text-dark d-block mb-1"><?php echo e(\Illuminate\Support\Str::limit($task->task_desc, 60)); ?></span>
                                    <div class="d-flex align-items-center gap-2 mt-2">
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill small">
                                            <i class="fas fa-check me-1"></i> منجزة
                                        </span>
                                        <?php if($task->attachment_url): ?>
                                            <a href="<?php echo e($task->attachment_url); ?>" target="_blank" class="badge bg-light text-primary border border-primary text-decoration-none rounded-pill small hover-zoom">
                                                <i class="fas fa-link me-1"></i> المرفق
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                
                                <td>
                                    <a href="<?php echo e(route('clients.show', $task->client_id)); ?>" class="text-decoration-none fw-bold text-primary hover-zoom d-inline-block">
                                        <i class="fas fa-building me-1 opacity-50"></i> <?php echo e($task->client->name ?? 'عميل محذوف'); ?>

                                    </a>
                                </td>

                                <td>
                                    <?php if($task->assignedUsers->count() > 0): ?>
                                        <?php $__currentLoopData = $task->assignedUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignedUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge bg-secondary mb-1"><i class="fas fa-user me-1 opacity-50"></i> <?php echo e($assignedUser->username); ?></span><br>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <span class="text-muted small">غير محدد</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="text-muted small fw-bold">
                                        <i class="far fa-calendar-alt me-1 text-primary"></i> 
                                        <?php echo e(\Carbon\Carbon::parse($task->completed_at ?? $task->updated_at)->format('Y-m-d')); ?>

                                        <br>
                                        <i class="far fa-clock me-1 text-warning mt-1"></i>
                                        <?php echo e(\Carbon\Carbon::parse($task->completed_at ?? $task->updated_at)->format('h:i A')); ?>

                                    </div>
                                </td>

                                <td class="text-start" style="max-width: 250px;">
                                    
                                    <?php if(!empty($task->completion_reply)): ?>
                                        <div class="p-2 bg-light rounded small text-dark border-start border-3 border-success shadow-sm" style="line-height: 1.5;">
                                            <?php echo nl2br(e($task->completion_reply)); ?>

                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small"><i class="fas fa-minus"></i> لم يُكتب تقرير</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="py-5 text-center text-muted">
                                    <i class="fas fa-clipboard-check fa-3x mb-3 text-light"></i>
                                    <h5>لا توجد مهام منجزة تطابق بحثك حالياً</h5>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php if($completedTasks->hasPages()): ?>
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-center">
                    <?php echo e($completedTasks->appends(request()->query())->links('pagination::bootstrap-5')); ?>

                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\crm\accounting-crm\resources\views/reports/completed_tasks.blade.php ENDPATH**/ ?>