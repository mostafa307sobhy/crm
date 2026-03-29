

<?php $__env->startSection('title', 'سجل الرقابة النظامية'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h4 class="fw-bold text-dark mb-0">
            <i class="fas fa-shield-alt text-danger me-2"></i> سجل الرقابة النظامية
        </h4>
        <p class="text-muted small mt-1 mb-0">مراقبة حركات الموظفين والأحداث</p>
    </div>
    <div class="col-md-6 text-end">
        <form action="<?php echo e(route('system_logs.index')); ?>" method="GET" class="input-group shadow-sm rounded-3" style="max-width: 350px; margin-left: 0; margin-right: auto;">
    <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control border-0" placeholder="ابحث في جميع السجلات..." required>
    <button type="submit" class="btn btn-primary px-3"><i class="fas fa-search"></i></button>
    <?php if(request('search')): ?>
        <a href="<?php echo e(route('system_logs.index')); ?>" class="btn btn-danger px-3" title="إلغاء البحث"><i class="fas fa-times"></i></a>
    <?php endif; ?>
</form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th class="px-4 py-3">الموظف</th>
                        <th>نوع الحركة</th>
                        <th>التفاصيل</th>
                        <th>IP Address</th>
                        <th>التاريخ والوقت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            // تلوين الشارة حسب نوع الحركة (نفس اللوجيك القديم)
                            $type = strtoupper($log->action_type);
                            $badgeClass = 'bg-secondary';
                            if (Str::contains($type, ['DELETE', 'CLEAR', 'ERROR'])) $badgeClass = 'bg-danger';
                            elseif (Str::contains($type, ['ADD', 'UPLOAD', 'COMPLETE'])) $badgeClass = 'bg-success';
                            elseif (Str::contains($type, ['UPDATE', 'CHANGE', 'TOGGLE'])) $badgeClass = 'bg-warning text-dark';
                            elseif (Str::contains($type, ['LOGIN', 'LOGOUT'])) $badgeClass = 'bg-info text-dark';
                        ?>
                        <tr class="log-row">
                            <td class="px-4 fw-bold text-dark">
                                <i class="fas fa-user-circle text-muted me-1"></i> 
                                <?php echo e($log->user ? $log->user->username : 'نظام/محذوف'); ?>

                            </td>
                            <td>
                                <span class="badge <?php echo e($badgeClass); ?> rounded-pill px-3">
                                    <?php echo e($type); ?>

                                </span>
                            </td>
                            <td class="text-dark log-details" style="max-width: 300px;">
                                <?php echo e($log->action_details); ?>

                            </td>
                            <td class="text-muted small" dir="ltr">
                                <?php echo e($log->ip_address ?? '---'); ?>

                            </td>
                            <td class="text-muted small" dir="ltr">
                                <?php echo e(\Carbon\Carbon::parse($log->created_at)->format('Y-m-d')); ?> <br>
                                <span class="fw-bold"><?php echo e(\Carbon\Carbon::parse($log->created_at)->format('h:i A')); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fas fa-history fa-3x mb-3 text-secondary opacity-50"></i>
                                <p class="mb-0 fw-bold">لا توجد سجلات حالية.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="card-footer bg-white border-top-0 d-flex justify-content-center pt-4 pb-3" dir="ltr">
            <?php echo e($logs->withQueryString()->links()); ?>

        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\crm\accounting-crm\resources\views/system_logs/index.blade.php ENDPATH**/ ?>