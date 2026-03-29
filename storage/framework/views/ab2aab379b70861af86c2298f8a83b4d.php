

<?php $__env->startSection('title', 'الرئيسية'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .custom-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 3px solid transparent;
        border-radius: 0;
        transition: all 0.3s;
    }
    .custom-tabs .nav-link:hover {
        color: #0d6efd;
    }
    .custom-tabs .nav-link.active {
        color: #0d6efd !important;
        border-bottom: 3px solid #0d6efd !important;
        background-color: transparent !important;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
    <h4 class="fw-bold text-dark mb-0">
        <i class="fas fa-tachometer-alt text-primary me-2"></i> نظرة عامة
    </h4>
    <div>
        <?php if(auth()->user()->role === 'admin'): ?>
        <button data-bs-toggle="modal" data-bs-target="#addClientModal" class="btn btn-outline-primary fw-bold rounded-pill shadow-sm me-2">
            <i class="fas fa-user-plus"></i> إضافة عميل جديد
        </button>
        <button data-bs-toggle="modal" data-bs-target="#addTaskModal" class="btn btn-gold fw-bold rounded-pill shadow-sm">
            <i class="fas fa-plus"></i> إضافة مهمة سريعة
        </button>
        <?php endif; ?>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 text-center p-3" style="background: linear-gradient(45deg, #198754, #20c997); color: white;">
            <i class="fas fa-user-tie fa-2x mb-2 opacity-75"></i>
            <h6 class="fw-bold mt-2"><?php echo e($isAdmin ? 'إجمالي العملاء' : 'عملائي المخصصين'); ?></h6>
            <h3 class="fw-bold mb-0"><?php echo e($activeClientsCount); ?></h3>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 text-center p-3" style="background: linear-gradient(45deg, #0d6efd, #0dcaf0); color: white;">
            <i class="fas fa-tasks fa-2x mb-2 opacity-75"></i>
            <h6 class="fw-bold mt-2"><?php echo e($isAdmin ? 'إجمالي المعلقة' : 'مهامي المعلقة'); ?></h6>
            <h3 class="fw-bold mb-0"><?php echo e($pendingTasksCount); ?></h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 text-center p-3" style="background: linear-gradient(45deg, #6c757d, #adb5bd); color: white;">
            <i class="fas fa-list-ol fa-2x mb-2 opacity-75"></i>
            <h6 class="fw-bold mt-2"><?php echo e($isAdmin ? 'إجمالي المهام' : 'إجمالي مهامي'); ?></h6>
            <h3 class="fw-bold mb-0"><?php echo e($totalTasksCount); ?></h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 text-center p-3" style="background: linear-gradient(45deg, #dc3545, #f87171); color: white;">
            <i class="fas fa-check-double fa-2x mb-2 opacity-75"></i>
            <h6 class="fw-bold mt-2"><?php echo e($isAdmin ? 'المهام المنجزة' : 'مهامي المنجزة'); ?></h6>
            <h3 class="fw-bold mb-0"><?php echo e($completedTasksCount); ?></h3>
        </div>
    </div>
</div>

<form method="GET" action="<?php echo e(route('dashboard')); ?>" class="card border-0 shadow-sm rounded-4 mt-2 mb-4 p-3 bg-white">
    <div class="row g-2 align-items-end">
        <div class="col-md-2">
            <label class="small fw-bold text-muted mb-1"><i class="fas fa-search text-info"></i> بحث بكلمة</label>
            <input type="text" name="search_text" value="<?php echo e(request('search_text')); ?>" class="form-control shadow-sm border-light" placeholder="ابحث في التفاصيل...">
        </div>
        <div class="col-md-2">
            <label class="small fw-bold text-muted mb-1"><i class="fas fa-building"></i> العميل</label>
            <select name="client_id" class="form-select shadow-sm border-light">
                <option value="">-- الكل --</option>
                <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->id); ?>" <?php echo e(request('client_id') == $c->id ? 'selected' : ''); ?>><?php echo e(Str::limit($c->name, 20)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        
        <?php if($isAdmin): ?>
        <div class="col-md-2">
            <label class="small fw-bold text-muted mb-1"><i class="fas fa-user-tie"></i> المكلف</label>
            <select name="user_id" class="form-select shadow-sm border-light">
                <option value="">-- الكل --</option>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($u->id); ?>" <?php echo e(request('user_id') == $u->id ? 'selected' : ''); ?>><?php echo e($u->username); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <?php endif; ?>

        <div class="col-md-2">
            <label class="small fw-bold text-muted mb-1"><i class="fas fa-calendar-alt text-primary"></i> من تاريخ</label>
            <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="form-control shadow-sm border-light">
        </div>
        <div class="col-md-2">
            <label class="small fw-bold text-muted mb-1"><i class="fas fa-calendar-check text-danger"></i> إلى تاريخ</label>
            <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="form-control shadow-sm border-light">
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-primary flex-grow-1 fw-bold shadow-sm"><i class="fas fa-search"></i></button>
            <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-light shadow-sm border" title="إلغاء الفلتر"><i class="fas fa-times text-danger"></i></a>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm rounded-4 mt-4">
    <div class="card-header bg-white border-bottom pt-3 pb-0 px-4">
        <ul class="nav nav-tabs border-0 custom-tabs" id="tasksTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold px-4 py-3" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                    <i class="fas fa-hourglass-half me-1"></i> المهام المعلقة
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-4 py-3" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab">
                    <i class="fas fa-check-circle me-1"></i> المهام المنجزة
                </button>
            </li>
        </ul>
    </div>
    
    <div class="card-body p-0">
        <div class="tab-content" id="tasksTabsContent">
            
            <div class="tab-pane fade show active" id="pending" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start px-4 py-3">تفاصيل المهمة</th>
                                <th>العميل</th>
                                <th>التواريخ والتقييم</th>
                                <th>الأولوية</th>
                                <th>المكلفون</th>
                                <th>إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $pendingTasksList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="<?php echo e(\Carbon\Carbon::parse($task->deadline)->isPast() ? 'table-danger border-danger' : ''); ?>">
                                    <td class="text-start px-3 fw-bold text-dark" style="white-space: pre-line; line-height: 1.3; font-size: 0.85rem;">
                                        <?php echo e(Str::limit($task->task_desc, 100)); ?>

                                        <?php if($task->recurrence_type != 'none'): ?>
                                            <span class="badge bg-light text-primary border ms-1" title="مهمة متكررة"><i class="fas fa-sync-alt"></i> دورية</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('clients.show', $task->client_id)); ?>" class="text-decoration-none" title="الذهاب لملف العميل">
                                            <span class="badge bg-light text-dark border p-2" style="transition: 0.3s;"><i class="fas fa-building text-primary me-1"></i> <?php echo e($task->client->name ?? 'غير محدد'); ?></span>
                                        </a>
                                    </td>
                                    <td style="min-width: 240px;">
                                        <?php
                                            $now = \Carbon\Carbon::now();
                                            $deadline = \Carbon\Carbon::parse($task->deadline);
                                            $createdAt = $task->request_date ? \Carbon\Carbon::parse($task->request_date) : $task->created_at;
                                            
                                            $isLate = $now->greaterThan($deadline);
                                            $diff = $now->diff($deadline);
                                            
                                            $badgeClass = '';
                                            $icon = '';
                                            $statusText = '';

                                            if ($isLate) {
                                                $badgeClass = 'bg-danger text-white';
                                                $icon = 'fas fa-exclamation-circle';
                                                $statusText = $diff->d > 0 ? "متأخرة " . $diff->d . " يوم!" : "متأخرة " . $diff->h . " ساعة!";
                                            } else {
                                                $badgeClass = 'text-dark'; 
                                                $badgeStyle = 'background-color: #0dcaf0;'; 
                                                $icon = 'fas fa-hourglass-half';
                                                $statusText = $diff->d > 0 ? "متبقي " . $diff->d . " يوم و " . $diff->h . " ساعة" : "متبقي " . $diff->h . " ساعة";
                                            }
                                        ?>

                                        <div class="border rounded-3 p-1 bg-white mx-auto shadow-sm" style="max-width: 230px; font-size: 0.75rem;">
                                            <div class="d-flex justify-content-between align-items-center px-1">
                                                <span class="text-muted">الطلب: <i class="fas fa-clock text-secondary ms-1"></i></span>
                                                <span class="fw-bold text-dark" dir="ltr"><?php echo e($createdAt->format('Y-m-d h:i A')); ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-1 px-1">
                                                <span class="text-muted">التسليم: <i class="fas fa-bullseye text-secondary ms-1"></i></span>
                                                <span class="fw-bold text-primary" dir="ltr"><?php echo e($deadline->format('Y-m-d h:i A')); ?></span>
                                            </div>
                                            <div class="text-center">
                                                <span class="badge rounded-pill <?php echo e($badgeClass); ?> px-2 py-1 shadow-sm" style="font-size: 0.7rem; <?php echo e($badgeStyle ?? ''); ?>">
                                                    <?php echo e($statusText); ?> <i class="<?php echo e($icon); ?> ms-1"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($task->priority == 'high'): ?> <span class="badge bg-danger rounded-pill">عالية</span>
                                        <?php elseif($task->priority == 'medium'): ?> <span class="badge bg-warning text-dark rounded-pill">متوسطة</span>
                                        <?php else: ?> <span class="badge bg-info text-dark rounded-pill">عادية</span> <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php $__currentLoopData = $task->assignedUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignedUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge bg-secondary mb-1"><?php echo e($assignedUser->username); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <form action="<?php echo e(route('tasks.complete', $task->id)); ?>" method="POST" onsubmit="confirmAction(event, this, 'هل تريد بالفعل إنجاز هذه المهمة؟', 'question', '#198754');">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-success rounded-circle d-flex justify-content-center align-items-center" title="إنجاز" style="width: 32px; height: 32px; padding: 0;">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            
                                            <?php if(auth()->user()->role === 'admin'): ?>
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-circle d-flex justify-content-center align-items-center" title="تعديل المهمة" style="width: 32px; height: 32px; padding: 0;"
                                                data-id="<?php echo e($task->id); ?>"
                                                data-desc="<?php echo e($task->task_desc); ?>"
                                                data-deadline="<?php echo e($task->deadline); ?>"
                                                data-priority="<?php echo e($task->priority); ?>"
                                                data-client="<?php echo e($task->client_id); ?>"
                                                data-recurrence="<?php echo e($task->recurrence_type); ?>"
                                                data-recurrence-end="<?php echo e($task->recurrence_end_date); ?>"
                                                data-users="<?php echo e(json_encode($task->assignedUsers->pluck('id'))); ?>"
                                                onclick="openEditTaskModal(this)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <form action="<?php echo e(route('tasks.destroy', $task->id)); ?>" method="POST" onsubmit="confirmAction(event, this, 'هل أنت متأكد من حذف هذه المهمة نهائياً؟ لا يمكن التراجع عن هذا الإجراء!', 'error', '#dc3545');">
                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle d-flex justify-content-center align-items-center" title="حذف" style="width: 32px; height: 32px; padding: 0;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4 fw-bold">لا توجد مهام معلقة.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-4" dir="ltr">
                        <?php echo e($pendingTasksList->withQueryString()->links()); ?>

                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="completed" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start px-4 py-3">تفاصيل المهمة</th>
                                <th>العميل</th>
                                <th class="text-center">التواريخ والتقييم</th>
                                <th>المكلفون</th>
                                <th>إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $completedTasksList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="table-light opacity-75">
                                    <td class="text-start px-3 fw-bold text-muted text-decoration-line-through" style="white-space: pre-line; line-height: 1.3; font-size: 0.85rem;">
                                        <?php echo e(Str::limit($task->task_desc, 100)); ?>

                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('clients.show', $task->client_id)); ?>" class="text-decoration-none" title="الذهاب لملف العميل">
                                            <span class="badge bg-light text-dark border p-2" style="transition: 0.3s;"><i class="fas fa-building text-primary me-1"></i> <?php echo e($task->client->name ?? 'غير محدد'); ?></span>
                                        </a>
                                    </td>
                                    <td style="min-width: 240px;">
                                        <?php
                                            $deadline = \Carbon\Carbon::parse($task->deadline);
                                            $completedAt = $task->completed_at ? \Carbon\Carbon::parse($task->completed_at) : $task->updated_at;
                                            
                                            $wasLate = $completedAt->greaterThan($deadline);
                                            
                                            $badgeClass = $wasLate ? 'bg-danger text-white' : 'bg-success text-white';
                                            $icon = $wasLate ? 'fas fa-exclamation-triangle' : 'fas fa-check-double';
                                            $statusText = $wasLate ? "أُنجزت متأخرة" : "أُنجزت في الموعد";
                                        ?>

                                        <div class="border rounded-3 p-1 bg-light shadow-sm mx-auto" style="max-width: 230px; font-size: 0.75rem;">
                                            <div class="d-flex justify-content-between align-items-center px-1">
                                                <span class="text-muted">الموعد: <i class="fas fa-bullseye text-secondary ms-1"></i></span>
                                                <span class="fw-bold text-dark" dir="ltr"><?php echo e($deadline->format('Y-m-d h:i A')); ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-1 px-1">
                                                <span class="text-muted">الإنجاز: <i class="fas fa-flag-checkered text-success ms-1"></i></span>
                                                <span class="fw-bold text-success" dir="ltr"><?php echo e($completedAt->format('Y-m-d h:i A')); ?></span>
                                            </div>
                                            <div class="text-center">
                                                <span class="badge rounded-pill <?php echo e($badgeClass); ?> px-2 py-1 shadow-sm" style="font-size: 0.7rem;">
                                                    <?php echo e($statusText); ?> <i class="<?php echo e($icon); ?> ms-1"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php $__currentLoopData = $task->assignedUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignedUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge bg-secondary mb-1"><?php echo e($assignedUser->username); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                    <td>
                                        <?php if(auth()->user()->role === 'admin'): ?>
                                        <div class="d-flex justify-content-center gap-2">
                                            <form action="<?php echo e(route('tasks.undo', $task->id)); ?>" method="POST" onsubmit="confirmAction(event, this, 'هل تريد التراجع عن الإنجاز وإعادتها لقائمة المهام المعلقة؟', 'warning', '#ffc107');">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-warning rounded-circle d-flex justify-content-center align-items-center" title="تراجع" style="width: 32px; height: 32px; padding: 0;">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="<?php echo e(route('tasks.destroy', $task->id)); ?>" method="POST" onsubmit="confirmAction(event, this, 'هل أنت متأكد من حذف هذه المهمة نهائياً؟ لا يمكن التراجع عن هذا الإجراء!', 'error', '#dc3545');">
                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle d-flex justify-content-center align-items-center" title="حذف" style="width: 32px; height: 32px; padding: 0;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <?php else: ?>
                                            <span class="badge bg-success rounded-pill px-3"><i class="fas fa-check-double"></i> منجزة</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="5" class="text-center text-muted py-4 fw-bold">لا توجد مهام منجزة.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-4" dir="ltr">
                        <?php echo e($completedTasksList->withQueryString()->links()); ?>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    // دالة بديلة لرسائل التأكيد المزعجة
    function confirmAction(event, formElement, message, iconType = 'warning', confirmColor = '#3085d6') {
        event.preventDefault(); // نوقف إرسال الفورم مؤقتاً
        
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: message,
            icon: iconType,
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، تأكيد!',
            cancelButtonText: 'إلغاء',
            customClass: {
                popup: 'rounded-4 shadow-lg border-0'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                formElement.submit(); // إذا وافق، نرسل الفورم
            }
        });
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\crm\accounting-crm\resources\views/dashboard.blade.php ENDPATH**/ ?>