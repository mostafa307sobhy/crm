

<?php $__env->startSection('title', 'التقارير والإحصائيات الشاملة'); ?>

<?php $__env->startSection('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
    .report-card { border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: transform 0.3s; }
    .report-card:hover { transform: translateY(-5px); }
    .icon-box { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; flex-shrink: 0; }
    #calendar { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .fc-event { cursor: pointer; }
    .timeline-item { padding-left: 1.5rem; position: relative; border-right: 2px solid #e9ecef; margin-right: 10px; padding-right: 20px; border-left: none;}
    .timeline-item::before { content: ''; position: absolute; right: -6px; top: 0; width: 10px; height: 10px; border-radius: 50%; background: #0d6efd; }
    
    @keyframes pulse-red { 0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); } 100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); } }
    .pulse-danger { animation: pulse-red 2s infinite; }
    
    .aging-box { border-radius: 12px; padding: 12px; text-align: center; border: 1px solid #eee; transition: 0.2s; }
    .aging-box:hover { transform: scale(1.02); }

    @media print {
        body { background-color: #fff !important; }
        #sidebar, .top-navbar, .btn, #periodFilter, .fc-toolbar, .no-print { display: none !important; }
        #content { margin: 0 !important; width: 100% !important; padding: 0 !important; }
        .report-card, .card { box-shadow: none !important; border: 1px solid #ddd !important; break-inside: avoid; }
        .col-md-3, .col-md-4, .col-md-6, .col-md-8 { width: 50% !important; float: right; padding: 10px; }
        .row { display: flex; flex-wrap: wrap; }
        canvas { max-height: 220px !important; }
        .timeline-item { page-break-inside: avoid; }
        .card-body { padding: 10px !important; }
        h4.fw-bold { text-align: center; margin-bottom: 30px !important; font-size: 24px; width: 100%; }
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <h4 class="fw-bold text-dark mb-0"><i class="fas fa-chart-line text-primary me-2"></i> التقارير والإحصائيات الشاملة</h4>
    
    <div class="d-flex gap-2 align-items-center">
        <select id="periodFilter" class="form-select shadow-sm fw-bold border-0" style="width: 160px; border-radius: 10px; background-color: #E1EDFF; color: var(--primary-color);" onchange="updateDashboard(this.value)">
            <option value="all" selected>كل الأوقات</option>
            <option value="today">اليوم</option>
            <option value="week">هذا الأسبوع</option>
            <option value="month">هذا الشهر</option>
            <option value="year">هذا العام</option>
        </select>

        <button onclick="window.print()" class="btn btn-outline-secondary fw-bold shadow-sm" style="border-radius: 10px;">
            <i class="fas fa-print"></i> طباعة التقرير
        </button>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card report-card bg-white h-100 position-relative border-bottom border-success border-4">
            <a href="<?php echo e(route('clients.index')); ?>" class="stretched-link no-print"></a>
            <div class="card-body d-flex align-items-center p-3">
                <div class="icon-box" style="background: linear-gradient(45deg, #198754, #20c997);"><i class="fas fa-money-bill-wave"></i></div>
                <div class="ms-3 me-2 flex-grow-1">
                    <p class="text-muted small mb-1 fw-bold">إجمالي الإيرادات</p>
                    <h4 class="mb-0 fw-bold text-success" id="stat-revenue"><?php echo e(number_format($stats['total_revenue'])); ?></h4>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card report-card bg-white h-100 position-relative border-bottom border-success border-4">
            <a href="#renewals-section" class="stretched-link no-print"></a>
            <div class="card-body d-flex align-items-center p-3">
                <div class="icon-box" style="background: linear-gradient(45deg, #198754, #8fbc8f);"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="ms-3 me-2 flex-grow-1">
                    <p class="text-muted small mb-1 fw-bold">تجديدات هذا الأسبوع</p>
                    <h4 class="mb-0 fw-bold text-success" id="stat-cashflow"><?php echo e(number_format($stats['expected_cash_flow_total'] ?? 0)); ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card report-card bg-white h-100 position-relative border-bottom border-primary border-4">
            <a href="<?php echo e(route('clients.index')); ?>" class="stretched-link no-print"></a>
            <div class="card-body d-flex align-items-center p-3">
                <div class="icon-box" style="background: linear-gradient(45deg, #0d6efd, #0dcaf0);"><i class="fas fa-users"></i></div>
                <div class="ms-3 me-2 flex-grow-1">
                    <p class="text-muted small mb-1 fw-bold">العملاء</p>
                    <h4 class="mb-0 fw-bold text-primary" id="stat-clients"><?php echo e($stats['clients_total']); ?></h4>
                    <small class="text-muted fw-bold" id="stat-active-clients"><span class="text-primary"><?php echo e($stats['clients_active']); ?> نشط</span></small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card report-card bg-white h-100 position-relative border-bottom border-secondary border-4">
            <a href="<?php echo e(route('users.index')); ?>" class="stretched-link no-print"></a>
            <div class="card-body d-flex align-items-center p-3">
                <div class="icon-box" style="background: linear-gradient(45deg, #6c757d, #adb5bd);"><i class="fas fa-user-tie"></i></div>
                <div class="ms-3 me-2 flex-grow-1">
                    <p class="text-muted small mb-1 fw-bold">فريق العمل</p>
                    <h4 class="mb-0 fw-bold text-secondary" id="stat-users"><?php echo e($stats['users_total']); ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card report-card bg-white h-100 position-relative border-bottom border-info border-4"> 
            <a href="<?php echo e(route('completed_tasks.index')); ?>" class="stretched-link no-print"></a> 
            <div class="card-body d-flex align-items-center p-3">
                <div class="icon-box" style="background: linear-gradient(45deg, #0dcaf0, #80e6fc);"><i class="fas fa-check-double"></i></div>
                <div class="ms-3 me-2 flex-grow-1">
                    <p class="text-muted small mb-1 fw-bold">مهام منجزة</p>
                    <h4 class="mb-0 fw-bold text-info" id="stat-completed"><?php echo e($stats['tasks_completed']); ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card report-card bg-white h-100 position-relative border-bottom border-secondary border-4">
            <div class="card-body d-flex align-items-center p-3">
                <div class="icon-box" style="background: linear-gradient(45deg, #6c757d, #ced4da);"><i class="fas fa-stopwatch"></i></div>
                <div class="ms-3 me-2 flex-grow-1">
                    <p class="text-muted small mb-1 fw-bold">متوسط سرعة الإنجاز</p>
                    <h4 class="mb-0 fw-bold text-secondary"><span id="stat-avg-res"><?php echo e($stats['avg_resolution_hours'] ?? 0); ?></span> <span class="fs-6">ساعة</span></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card report-card bg-white h-100 position-relative border-bottom border-warning border-4">
            <a href="<?php echo e(route('pending_tasks.index')); ?>" class="stretched-link no-print"></a>
            <div class="card-body d-flex align-items-center p-3">
                <div class="icon-box" style="background: linear-gradient(45deg, #ffc107, #ffda6a);"><i class="fas fa-tasks text-dark"></i></div>
                <div class="ms-3 me-2 flex-grow-1">
                    <p class="text-muted small mb-1 fw-bold">مهام معلقة</p>
                    <h4 class="mb-0 fw-bold text-warning" id="stat-pending"><?php echo e($stats['tasks_pending']); ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card report-card bg-white h-100 position-relative border-bottom border-danger border-4 pulse-danger">
            <a href="<?php echo e(route('pending_tasks.index', ['filter' => 'overdue'])); ?>" class="stretched-link no-print"></a>
            <div class="card-body d-flex align-items-center p-3">
                <div class="icon-box" style="background: linear-gradient(45deg, #dc3545, #f87171);"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="ms-3 me-2 flex-grow-1">
                    <p class="text-danger small mb-1 fw-bold">تأخيرات (SLA)</p>
                    <h4 class="mb-0 fw-bold text-danger" id="stat-overdue"><?php echo e($stats['tasks_overdue'] ?? 0); ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-5 mb-3 px-2 d-flex align-items-center">
    <i class="fas fa-microchip text-primary fs-4 me-2"></i>
    <h5 class="fw-bold text-dark mb-0">التحليلات العميقة وتجديدات العملاء</h5>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0"><h6 class="fw-bold"><i class="fas fa-chart-area text-primary me-1"></i> الخط الزمني للإنتاجية (مهام منجزة)</h6></div>
            <div class="card-body" style="height: 300px;"><canvas id="productivityChart"></canvas></div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-2"><h6 class="fw-bold"><i class="fas fa-hourglass-half text-warning me-1"></i> أعمار المهام المعلقة (Aging)</h6></div>
            <div class="card-body d-flex flex-column justify-content-center gap-3">
                <div class="aging-box bg-success bg-opacity-10 border-success border-opacity-25">
                    <h6 class="text-success fw-bold mb-1">حديثة (1-3 أيام)</h6>
                    <h3 class="fw-bold text-success mb-0" id="aging-new"><?php echo e($aging['new'] ?? 0); ?></h3>
                </div>
                <div class="aging-box bg-warning bg-opacity-10 border-warning border-opacity-50">
                    <h6 class="text-warning text-dark fw-bold mb-1">تحذير (4-7 أيام)</h6>
                    <h3 class="fw-bold text-dark mb-0" id="aging-warning"><?php echo e($aging['warning'] ?? 0); ?></h3>
                </div>
                <div class="aging-box bg-danger bg-opacity-10 border-danger border-opacity-25">
                    <h6 class="text-danger fw-bold mb-1">متعفنة (أكثر من 7 أيام)</h6>
                    <h3 class="fw-bold text-danger mb-0" id="aging-danger"><?php echo e($aging['danger'] ?? 0); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6" id="renewals-section">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom pt-3 pb-2 d-flex justify-content-between">
                <h6 class="fw-bold text-dark mb-0"><i class="fas fa-calendar-check text-success me-1"></i> تجديدات الاشتراكات (خلال أسبوع)</h6>
                <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 fs-6" id="cash-flow-total"><?php echo e(number_format($stats['expected_cash_flow_total'])); ?> ريال</span>
            </div>
            <div class="card-body p-0" id="cash-flow-list" style="max-height: 280px; overflow-y: auto;">
                <?php $__empty_1 = true; $__currentLoopData = $cashFlowClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cfc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                        <div>
                            <h6 class="mb-1 fw-bold text-dark">
                                <a href="<?php echo e(route('clients.show', $cfc->id)); ?>" class="text-decoration-none text-dark hover-zoom d-inline-block"><?php echo e($cfc->name); ?> <i class="fas fa-external-link-alt small ms-1 opacity-50"></i></a>
                            </h6>
                            <small class="text-muted"><i class="far fa-calendar-alt text-danger"></i> ينتهي في: <?php echo e(\Carbon\Carbon::parse($cfc->sub_end_date)->format('Y-m-d')); ?></small>
                        </div>
                        <div class="fw-bold text-success"><?php echo e(number_format($cfc->subscription_amount)); ?> ريال</div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center p-4 text-muted small">لا توجد اشتراكات تنتهي خلال الأيام الـ 7 القادمة.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0"><h6 class="fw-bold"><i class="fas fa-balance-scale text-primary me-1"></i> خريطة حِمل العمل (مهام معلقة للموظفين)</h6></div>
            <div class="card-body" style="height: 280px;"><canvas id="workloadChart"></canvas></div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom pt-3 pb-2"><h6 class="fw-bold text-dark mb-0"><i class="fas fa-crown text-warning me-2"></i> عملاء الـ VIP (الأكثر استهلاكاً للمهام)</h6></div>
            <div class="card-body p-0" id="top-clients-list" style="max-height: 280px; overflow-y: auto;">
                <?php $__empty_1 = true; $__currentLoopData = $topClients ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php /** @var \App\Models\Client $c */ ?>
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom hover-zoom">
                        <div>
                            <h6 class="mb-1 fw-bold text-dark"><a href="<?php echo e(route('clients.show', $c->id)); ?>" class="text-decoration-none text-dark"><i class="fas fa-building text-primary me-2 opacity-50"></i><?php echo e($c->name); ?></a></h6>
                            <small class="text-muted fw-bold">العائد: <span class="text-success"><?php echo e($c->subscription_amount ? number_format($c->subscription_amount) . ' ريال' : 'غير محدد'); ?></span></small>
                        </div>
                        <div class="text-center">
                            <span class="badge bg-warning text-dark rounded-pill fs-6 shadow-sm"><?php echo e($c->tasks_count); ?></span>
                            <small class="d-block text-muted mt-1 fw-bold" style="font-size: 0.65rem;">مهمة</small>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center p-4 text-muted small">لا توجد بيانات للفترة المحددة.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0"><h6 class="fw-bold"><i class="fas fa-trophy text-success me-1"></i> أبطال الإنجاز (أفضل الموظفين)</h6></div>
            <div class="card-body" style="height: 280px;"><canvas id="empPerformanceChart"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div id="calendar" class="no-print"></div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom pt-3 pb-2">
                <h6 class="fw-bold text-dark mb-0"><i class="fas fa-history text-secondary me-2"></i> أحدث النشاطات (Live Feed)</h6>
            </div>
            <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                <?php $__empty_1 = true; $__currentLoopData = $recentOperations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $op): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $text = mb_strtolower($op->action_text, 'UTF-8');
                        $icon = 'fa-info-circle'; $color = '#6c757d';
                        if (str_contains($text, 'أنجز') || str_contains($text, 'مكتمل')) { $icon = 'fa-check-circle'; $color = '#198754'; }
                        elseif (str_contains($text, 'أضاف') || str_contains($text, 'إضافة') || str_contains($text, 'جديد')) { $icon = 'fa-plus-circle'; $color = '#0d6efd'; }
                        elseif (str_contains($text, 'حذف') || str_contains($text, 'تعطيل')) { $icon = 'fa-trash-alt'; $color = '#dc3545'; }
                        elseif (str_contains($text, 'مرفق') || str_contains($text, 'ملف')) { $icon = 'fa-paperclip'; $color = '#0dcaf0'; }
                        elseif (str_contains($text, 'رد') || str_contains($text, 'شات')) { $icon = 'fa-comment-dots'; $color = '#ffc107'; }
                    ?>
                    <div class="timeline-item mb-3" style="border-right-color: <?php echo e($color); ?>;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-bold text-dark small"><i class="fas <?php echo e($icon); ?> me-1" style="color: <?php echo e($color); ?>"></i> <?php echo e($op->user->username ?? 'نظام'); ?></span>
                            <span class="text-muted" style="font-size: 0.70rem;" dir="ltr"><?php echo e(\Carbon\Carbon::parse($op->created_at)->format('m-d h:i A')); ?></span>
                        </div>
                        <div class="mb-1 text-secondary" style="font-size: 0.8rem; line-height: 1.5;">
                            <?php echo $op->formatted_text ?? strip_tags($op->action_text, '<b><i><u><strong><em><br><a><span>'); ?>

                        </div>
                        <?php if($op->client): ?>
                            <a href="<?php echo e(route('clients.show', $op->client->id)); ?>" class="badge bg-light text-dark text-decoration-none border shadow-sm no-print mt-1 hover-zoom"><i class="fas fa-building text-muted"></i> <?php echo e($op->client->name); ?></a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center text-muted mt-5"><i class="fas fa-history fa-3x mb-3 opacity-25"></i><p>لا توجد نشاطات مسجلة.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/ar.js"></script>

<script>
    Chart.defaults.font.family = "'Tajawal', sans-serif";

    // 🟢 1. تهيئة الرسوم البيانية 🟢

    var ctxProd = document.getElementById('productivityChart').getContext('2d');
    var productivityChart = new Chart(ctxProd, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($prod_labels ?? [], 15, 512) ?>,
            datasets: [{
                label: 'مهام منجزة',
                data: <?php echo json_encode($prod_counts ?? [], 15, 512) ?>,
                borderColor: '#0d6efd', backgroundColor: 'rgba(13, 110, 253, 0.1)', borderWidth: 3, fill: true, tension: 0.4, pointBackgroundColor: '#0dcaf0', pointRadius: 4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });
    
    var ctxWl = document.getElementById('workloadChart').getContext('2d');
    var workloadChart = new Chart(ctxWl, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($wl_labels ?? [], 15, 512) ?>,
            datasets: [{
                label: 'مهام معلقة',
                data: <?php echo json_encode($wl_counts ?? [], 15, 512) ?>,
                backgroundColor: 'rgba(255, 193, 7, 0.8)',
                borderRadius: 5
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    var ctxEmp = document.getElementById('empPerformanceChart').getContext('2d');
    var empPerformanceChart = new Chart(ctxEmp, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($emp_labels ?? [], 15, 512) ?>,
            datasets: [{
                label: 'مهام منجزة',
                data: <?php echo json_encode($emp_counts ?? [], 15, 512) ?>,
                backgroundColor: 'rgba(25, 135, 84, 0.8)', 
                borderRadius: 5
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    // 🟢 2. التحديث الديناميكي (AJAX Filtering) 🟢
    function updateDashboard(period) {
        document.body.style.cursor = 'wait';
        fetch(`<?php echo e(route('reports.index')); ?>?period=${period}`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(response => response.json())
        .then(data => {
            document.body.style.cursor = 'default';

            document.getElementById('stat-revenue').innerText = new Intl.NumberFormat('en-US').format(data.stats.total_revenue);
            document.getElementById('stat-cashflow').innerText = new Intl.NumberFormat('en-US').format(data.stats.expected_cash_flow_total);
            document.getElementById('stat-clients').innerText = data.stats.clients_total;
            document.getElementById('stat-active-clients').innerHTML = `<span class="text-primary">${data.stats.clients_active} نشط</span>`;
            document.getElementById('stat-completed').innerText = data.stats.tasks_completed;
            document.getElementById('stat-pending').innerText = data.stats.tasks_pending;
            document.getElementById('stat-overdue').innerText = data.stats.tasks_overdue;
            document.getElementById('stat-avg-res').innerText = data.stats.avg_resolution_hours;

            document.getElementById('aging-new').innerText = data.aging.new;
            document.getElementById('aging-warning').innerText = data.aging.warning;
            document.getElementById('aging-danger').innerText = data.aging.danger;

            empPerformanceChart.data.labels = data.chart_data.employees.labels;
            empPerformanceChart.data.datasets[0].data = data.chart_data.employees.data;
            empPerformanceChart.update();

            productivityChart.data.labels = data.chart_data.productivity.labels;
            productivityChart.data.datasets[0].data = data.chart_data.productivity.data;
            productivityChart.update();

            workloadChart.data.labels = data.chart_data.workload.labels;
            workloadChart.data.datasets[0].data = data.chart_data.workload.data;
            workloadChart.update();

            // تحديث جدول عملاء الـ VIP
            let topClientsHtml = '';
            data.top_clients.forEach(c => {
                let amount = c.subscription_amount ? new Intl.NumberFormat('en-US').format(c.subscription_amount) + ' ريال' : 'غير محدد';
                topClientsHtml += `
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom hover-zoom">
                        <div>
                            <h6 class="mb-1 fw-bold text-dark"><a href="/clients/${c.id}" class="text-decoration-none text-dark"><i class="fas fa-building text-primary me-2 opacity-50"></i>${c.name}</a></h6>
                            <small class="text-muted fw-bold">العائد: <span class="text-success">${amount}</span></small>
                        </div>
                        <div class="text-center">
                            <span class="badge bg-warning text-dark rounded-pill fs-6 shadow-sm">${c.tasks_count}</span>
                            <small class="d-block text-muted mt-1 fw-bold" style="font-size: 0.65rem;">مهمة</small>
                        </div>
                    </div>`;
            });
            document.getElementById('top-clients-list').innerHTML = topClientsHtml || '<div class="text-center p-4 text-muted small">لا توجد بيانات للفترة المحددة.</div>';

            // 🟢 تحديث قائمة التجديدات 🟢
            let cfHtml = '';
            document.getElementById('cash-flow-total').innerText = new Intl.NumberFormat('en-US').format(data.stats.expected_cash_flow_total) + ' ريال';
            data.cash_flow_clients.forEach(cfc => {
                let dateStr = cfc.sub_end_date ? new Date(cfc.sub_end_date).toISOString().split('T')[0] : '';
                cfHtml += `
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                        <div>
                            <h6 class="mb-1 fw-bold text-dark">
                                <a href="/clients/${cfc.id}" class="text-decoration-none text-dark hover-zoom d-inline-block">${cfc.name} <i class="fas fa-external-link-alt small ms-1 opacity-50"></i></a>
                            </h6>
                            <small class="text-muted"><i class="far fa-calendar-alt text-danger"></i> ينتهي في: ${dateStr}</small>
                        </div>
                        <div class="fw-bold text-success">${new Intl.NumberFormat('en-US').format(cfc.subscription_amount)} ريال</div>
                    </div>`;
            });
            document.getElementById('cash-flow-list').innerHTML = cfHtml || '<div class="text-center p-4 text-muted small">لا توجد اشتراكات تنتهي خلال الأيام الـ 7 القادمة.</div>';

        })
        .catch(error => { console.error('Error fetching data:', error); document.body.style.cursor = 'default'; });
    }

    // 🟢 3. التقويم 🟢
    document.addEventListener('DOMContentLoaded', function() {
        var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
            initialView: 'dayGridMonth', locale: 'ar',
            headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,listWeek' },
            buttonText: { today: 'اليوم', month: 'شهر', week: 'أسبوع', list: 'قائمة' },
            events: <?php echo json_encode($events ?? [], 15, 512) ?>, eventDisplay: 'block', contentHeight: 'auto', dayMaxEvents: 3,
            eventClick: function(info) { Swal.fire({ title: info.event.title, text: info.event.extendedProps.desc, icon: 'info', confirmButtonText: 'إغلاق' }); }
        });
        calendar.render();
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\crm\accounting-crm\resources\views/reports/index.blade.php ENDPATH**/ ?>