

<?php $__env->startSection('title', 'قائمة العملاء'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .custom-tabs .nav-link { color: #6c757d; border: none; border-bottom: 3px solid transparent; border-radius: 0; transition: all 0.3s; }
    .custom-tabs .nav-link:hover { color: #0d6efd; }
    .custom-tabs .nav-link.active { color: #0d6efd !important; border-bottom: 3px solid #0d6efd !important; background-color: transparent !important; }
    
    /* تأثير جميل لزر الدخول الخاص بالموضع */
    .btn-enter-client { transition: all 0.3s ease; }
    .btn-enter-client:hover { transform: translateX(-5px); box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2) !important; }
</style>

<?php
    // قواميس الترجمة لعرض البيانات باللغة العربية داخل جدول الإدارة
    $packages_ar = ['basic' => 'أساسية', 'advanced' => 'متقدمة', 'professional' => 'احترافية', 'comprehensive' => 'شاملة', 'custom' => 'مخصصة'];
    $durations_ar = ['monthly' => 'شهري', 'quarterly' => 'ربع سنوي', 'semi_annual' => 'نصف سنوي', 'annual' => 'سنوي'];
?>

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h4 class="fw-bold text-dark mb-0">
            <i class="fas fa-users text-primary me-2"></i> <?php echo e(auth()->user()->role === 'admin' ? 'الإدارة الشاملة للعملاء' : 'العملاء المخصصين لي'); ?>

        </h4>
    </div>
    <div class="col-md-6 text-end">
        <?php if(auth()->user()->role === 'admin'): ?>
            <button data-bs-toggle="modal" data-bs-target="#addClientModal" class="btn btn-gold fw-bold shadow-sm">
                <i class="fas fa-plus"></i> إضافة عميل جديد
            </button>
        <?php endif; ?>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mt-2 overflow-hidden">
    
    <?php if(auth()->user()->role === 'admin'): ?>
        <div class="card-header bg-white border-bottom pt-3 pb-0 px-4">
            <ul class="nav nav-tabs border-0 custom-tabs" id="clientsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold px-4 py-3" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab">
                        <i class="fas fa-user-check me-1"></i> العملاء النشطين <span class="badge bg-primary rounded-pill ms-1"><?php echo e($activeClients->count()); ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold px-4 py-3" id="inactive-tab" data-bs-toggle="tab" data-bs-target="#inactive" type="button" role="tab">
                        <i class="fas fa-user-times text-danger me-1"></i> العملاء المعطلين <span class="badge bg-danger rounded-pill ms-1"><?php echo e($inactiveClients->count()); ?></span>
                    </button>
                </li>
            </ul>
        </div>
        
        <div class="card-body p-0">
            <div class="tab-content" id="clientsTabsContent">
                <div class="tab-pane fade show active" id="active" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle mb-0 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>اسم العميل</th>
                                    <th>قيمة الاشتراك</th>
                                    <th>الباقة</th>
                                    <th>بداية الاشتراك</th>
                                    <th>المدة</th>
                                    <th>تاريخ الانتهاء</th>
                                    <th>نطاق الرؤية</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $activeClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo e($client->name); ?></td>
                                        <td class="fw-bold text-success" dir="ltr"><?php echo e($client->subscription_amount ? number_format($client->subscription_amount, 2) . ' ريال' : '---'); ?></td>
                                        <td><span class="badge bg-light text-primary border"><?php echo e($packages_ar[$client->package_type] ?? 'غير محدد'); ?></span></td>
                                        <td dir="ltr" class="small text-muted fw-bold"><?php echo e($client->sub_start_date ?? '---'); ?></td>
                                        <td><?php echo e($durations_ar[$client->subscription_duration] ?? '---'); ?></td>
                                        <td dir="ltr" class="small text-danger fw-bold"><?php echo e($client->sub_end_date ?? '---'); ?></td>
                                        <td>
                                            <?php if($client->visibility == 'all'): ?>
                                                <span class="badge bg-success">متاح للجميع</span>
                                            <?php elseif($client->visibility == 'admins_only'): ?>
                                                <span class="badge bg-danger">للإدارة فقط</span>
                                            <?php else: ?>
                                                <?php $__currentLoopData = $client->assignedUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <span class="badge bg-secondary mb-1"><?php echo e($u->username); ?></span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="<?php echo e(route('clients.show', $client->id)); ?>" class="btn btn-sm btn-outline-primary rounded-circle d-flex justify-content-center align-items-center" title="عرض ملف العميل" style="width: 32px; height: 32px; padding: 0;"><i class="fas fa-eye"></i></a>
                                                <button type="button" class="btn btn-sm btn-outline-success rounded-circle d-flex justify-content-center align-items-center" title="تعديل بيانات العميل" style="width: 32px; height: 32px; padding: 0;" data-bs-toggle="modal" data-bs-target="#editClientModal<?php echo e($client->id); ?>"><i class="fas fa-edit"></i></button>
                                                <form action="<?php echo e(route('clients.destroy', $client->id)); ?>" method="POST" onsubmit="confirmAction(event, this, 'تحذير خطير: هل أنت متأكد من الحذف؟', 'error', '#dc3545');">
                                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle d-flex justify-content-center align-items-center" title="حذف العميل" style="width: 32px; height: 32px; padding: 0;"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="8" class="text-center text-muted py-4 fw-bold">لا يوجد عملاء نشطين.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="inactive" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle mb-0 text-center">
                            <thead class="table-secondary">
                                <tr>
                                    <th>اسم العميل</th>
                                    <th>قيمة الاشتراك</th>
                                    <th>الباقة</th>
                                    <th>بداية الاشتراك</th>
                                    <th>المدة</th>
                                    <th>تاريخ الانتهاء</th>
                                    <th>نطاق الرؤية</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $inactiveClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="table-light opacity-75">
                                        <td class="fw-bold text-muted text-decoration-line-through"><?php echo e($client->name); ?></td>
                                        <td class="fw-bold text-success" dir="ltr"><?php echo e($client->subscription_amount ? number_format($client->subscription_amount, 2) . ' ريال' : '---'); ?></td>
                                        <td><span class="badge bg-light text-muted border"><?php echo e($packages_ar[$client->package_type] ?? 'غير محدد'); ?></span></td>
                                        <td dir="ltr" class="small text-muted"><?php echo e($client->sub_start_date ?? '---'); ?></td>
                                        <td class="text-muted"><?php echo e($durations_ar[$client->subscription_duration] ?? '---'); ?></td>
                                        <td dir="ltr" class="small text-muted"><?php echo e($client->sub_end_date ?? '---'); ?></td>
                                        <td>
                                            <?php if($client->visibility == 'all'): ?>
                                                <span class="badge bg-secondary opacity-50">متاح للجميع</span>
                                            <?php elseif($client->visibility == 'admins_only'): ?>
                                                <span class="badge bg-secondary opacity-50">للإدارة فقط</span>
                                            <?php else: ?>
                                                <?php $__currentLoopData = $client->assignedUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <span class="badge bg-secondary mb-1 opacity-50"><?php echo e($u->username); ?></span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="<?php echo e(route('clients.show', $client->id)); ?>" class="btn btn-sm btn-outline-secondary rounded-circle d-flex justify-content-center align-items-center" title="عرض الملف (للقراءة)" style="width: 32px; height: 32px; padding: 0;"><i class="fas fa-eye"></i></a>
                                                <form action="<?php echo e(route('clients.destroy', $client->id)); ?>" method="POST" onsubmit="confirmAction(event, this, 'هل أنت متأكد من الحذف النهائي من الأرشيف؟', 'error', '#dc3545');">
                                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle d-flex justify-content-center align-items-center" title="حذف العميل نهائياً" style="width: 32px; height: 32px; padding: 0;"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="8" class="text-center text-muted py-4 fw-bold">لا يوجد عملاء معطلين.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="card-header bg-light border-bottom pt-4 pb-3 px-4">
            <h5 class="fw-bold text-primary mb-0"><i class="fas fa-briefcase me-2"></i> الشركات والعملاء بعهدتك</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th class="px-4 py-3">اسم العميل / الشركة</th>
                            <th class="text-end px-4">إجراءات الدخول</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $activeClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-white border rounded-circle d-flex justify-content-center align-items-center shadow-sm me-3" style="width: 45px; height: 45px;">
                                            <i class="fas fa-building text-primary fs-5"></i>
                                        </div>
                                        <span class="fw-bold text-dark fs-5"><?php echo e($client->name); ?></span>
                                    </div>
                                </td>
                                <td class="text-end px-4 py-3">
                                    <a href="<?php echo e(route('clients.show', $client->id)); ?>" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-bold btn-enter-client">
                                        الدخول لملف العميل <i class="fas fa-sign-in-alt ms-2"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="2" class="text-center text-muted py-5">
                                    <i class="fas fa-folder-open fa-4x mb-3 opacity-25 text-primary"></i>
                                    <h5 class="fw-bold">لا يوجد عملاء مخصصين لك حالياً.</h5>
                                    <p class="mb-0">يرجى مراجعة الإدارة لتخصيص العملاء لملفك.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php if(auth()->user()->role === 'admin'): ?>
    
    <?php $__currentLoopData = $activeClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="modal fade" id="editClientModal<?php echo e($client->id); ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit text-primary me-2"></i> تعديل بيانات: <?php echo e($client->name); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo e(route('clients.update', $client->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">اسم العميل</label>
                                <input type="text" name="name" value="<?php echo e($client->name); ?>" class="form-control shadow-sm border-light" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">حالة العميل</label>
                                <select name="status" class="form-select shadow-sm border-light" required>
                                    <option value="active" <?php echo e($client->status == 'active' ? 'selected' : ''); ?>>نشط</option>
                                    <option value="inactive" <?php echo e($client->status == 'inactive' ? 'selected' : ''); ?>>معطل</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">نوع الباقة</label>
                                <select name="package_type" class="form-select shadow-sm border-light" required>
                                    <option value="basic" <?php echo e($client->package_type == 'basic' ? 'selected' : ''); ?>>أساسية</option>
                                    <option value="advanced" <?php echo e($client->package_type == 'advanced' ? 'selected' : ''); ?>>متقدمة</option>
                                    <option value="professional" <?php echo e($client->package_type == 'professional' ? 'selected' : ''); ?>>احترافية</option>
                                    <option value="comprehensive" <?php echo e($client->package_type == 'comprehensive' ? 'selected' : ''); ?>>شاملة</option>
                                    <option value="custom" class="fw-bold text-primary" <?php echo e($client->package_type == 'custom' ? 'selected' : ''); ?>>مخصصة</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-success">قيمة الاشتراك (بالريال)</label>
                                <input type="number" step="0.01" name="subscription_amount" value="<?php echo e($client->subscription_amount); ?>" class="form-control shadow-sm border-success" placeholder="مثال: 1500">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">الرقم الضريبي</label>
                                <input type="text" name="tax_number" value="<?php echo e($client->tax_number); ?>" class="form-control shadow-sm border-light" placeholder="اختياري">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">السجل التجاري</label>
                                <input type="text" name="commercial_register" value="<?php echo e($client->commercial_register); ?>" class="form-control shadow-sm border-light" placeholder="اختياري">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold">بداية الاشتراك</label>
                                <input type="date" name="sub_start_date" id="edit_start_date_<?php echo e($client->id); ?>" value="<?php echo e(!empty($client->sub_start_date) ? \Carbon\Carbon::parse($client->sub_start_date)->format('Y-m-d') : ''); ?>" class="form-control shadow-sm border-light date-start-input" data-client-id="<?php echo e($client->id); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">مدة الاشتراك</label>
                                <select name="subscription_duration" id="edit_duration_<?php echo e($client->id); ?>" class="form-select shadow-sm border-light duration-select" data-client-id="<?php echo e($client->id); ?>">
                                    <option value="" <?php echo e(empty($client->subscription_duration) ? 'selected' : ''); ?>>غير محدد</option>
                                    <option value="monthly" <?php echo e($client->subscription_duration == 'monthly' ? 'selected' : ''); ?>>شهري</option>
                                    <option value="quarterly" <?php echo e($client->subscription_duration == 'quarterly' ? 'selected' : ''); ?>>ربع سنوي</option>
                                    <option value="semi_annual" <?php echo e($client->subscription_duration == 'semi_annual' ? 'selected' : ''); ?>>نصف سنوي</option>
                                    <option value="annual" <?php echo e($client->subscription_duration == 'annual' ? 'selected' : ''); ?>>سنوي</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-danger">تاريخ الانتهاء</label>
                                <input type="date" name="sub_end_date" id="edit_end_date_<?php echo e($client->id); ?>" value="<?php echo e(!empty($client->sub_end_date) ? \Carbon\Carbon::parse($client->sub_end_date)->format('Y-m-d') : ''); ?>" class="form-control shadow-sm border-light bg-light" readonly>
                            </div>
                            <div class="col-md-12 border-top pt-3 mt-3">
                                <label class="form-label fw-bold text-primary mb-3"><i class="fas fa-eye me-1"></i> نطاق رؤية العميل</label>
                                <div class="d-flex flex-wrap gap-4 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input visibility-radio" type="radio" name="visibility" id="vis_all_<?php echo e($client->id); ?>" value="all" <?php echo e($client->visibility == 'all' ? 'checked' : ''); ?>>
                                        <label class="form-check-label fw-bold text-success" for="vis_all_<?php echo e($client->id); ?>">متاح للجميع</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input visibility-radio" type="radio" name="visibility" id="vis_specific_<?php echo e($client->id); ?>" value="specific" <?php echo e($client->visibility == 'specific' ? 'checked' : ''); ?>>
                                        <label class="form-check-label fw-bold text-primary" for="vis_specific_<?php echo e($client->id); ?>">تخصيص لموظفين محددين</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input visibility-radio" type="radio" name="visibility" id="vis_admins_<?php echo e($client->id); ?>" value="admins_only" <?php echo e($client->visibility == 'admins_only' ? 'checked' : ''); ?>>
                                        <label class="form-check-label fw-bold text-danger" for="vis_admins_<?php echo e($client->id); ?>">مغلق للإدارة فقط</label>
                                    </div>
                                </div>

                                <div class="border rounded-3 p-3 bg-white shadow-sm employees-box" style="max-height: 150px; overflow-y: auto; <?php echo e($client->visibility == 'specific' ? 'display: block;' : 'display: none;'); ?>">
                                    <?php $assignedUserIds = $client->assignedUsers->pluck('id')->toArray(); ?>
                                    <?php $__currentLoopData = \App\Models\User::where('role', '!=', 'admin')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="form-check mb-2 border-bottom pb-1">
                                            <input class="form-check-input" type="checkbox" name="assigned_users[]" value="<?php echo e($u->id); ?>" id="edit_u_<?php echo e($u->id); ?>_c_<?php echo e($client->id); ?>" <?php echo e(in_array($u->id, $assignedUserIds) ? 'checked' : ''); ?>>
                                            <label class="form-check-label small text-dark fw-bold" for="edit_u_<?php echo e($u->id); ?>_c_<?php echo e($client->id); ?>"><?php echo e($u->username); ?></label>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

  
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // سكريبت إظهار وإخفاء قائمة الموظفين
        document.querySelectorAll('.visibility-radio').forEach(radio => {
            radio.addEventListener('change', function () {
                let box = this.closest('.col-md-12').querySelector('.employees-box');
                if (this.value === 'specific') {
                    box.style.display = 'block';
                } else {
                    box.style.display = 'none';
                    box.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
                }
            });
        });

        // 🟢 سكريبت حساب تاريخ الانتهاء تلقائياً لكل الـ Modals 🟢
        function calculateEndDate(clientId) {
            let startInput = document.getElementById('edit_start_date_' + clientId);
            let durationSelect = document.getElementById('edit_duration_' + clientId);
            let endInput = document.getElementById('edit_end_date_' + clientId);

            if (startInput && durationSelect && endInput) {
                let startDate = startInput.value;
                let duration = durationSelect.value;
                
                if (startDate && duration) {
                    let date = new Date(startDate);
                    if (duration === 'monthly') date.setMonth(date.getMonth() + 1);
                    else if (duration === 'quarterly') date.setMonth(date.getMonth() + 3);
                    else if (duration === 'semi_annual') date.setMonth(date.getMonth() + 6);
                    else if (duration === 'annual') date.setFullYear(date.getFullYear() + 1);
                    
                    endInput.value = date.toISOString().split('T')[0];
                } else {
                    endInput.value = '';
                }
            }
        }

        // تفعيل المستمع (Listener) لكل الحقول
        document.querySelectorAll('.date-start-input').forEach(input => {
            input.addEventListener('change', function() {
                calculateEndDate(this.getAttribute('data-client-id'));
            });
        });

        document.querySelectorAll('.duration-select').forEach(select => {
            select.addEventListener('change', function() {
                calculateEndDate(this.getAttribute('data-client-id'));
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\crm\accounting-crm\resources\views/clients/index.blade.php ENDPATH**/ ?>