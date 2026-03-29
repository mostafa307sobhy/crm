<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بيت المحاسبة - <?php echo $__env->yieldContent('title', 'لوحة التحكم'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #002E5D;
            --primary-dark: #001a35;
            --gold-color: #CFB065;
            --bg-color: #f4f7f6;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--bg-color);
            overflow-x: hidden;
        }

        /* ================= هيكل الصفحة (Layout) ================= */
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
            min-height: 100vh;
        }

        /* ================= القائمة الجانبية (Sidebar) ================= */
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: #fff;
            transition: all 0.3s ease;
            box-shadow: -4px 0 15px rgba(0,0,0,0.05);
            z-index: 1050;
        }

        #sidebar.active {
            margin-right: -260px; /* الإخفاء في الـ RTL */
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            background: rgba(0,0,0,0.1);
        }

        .sidebar-menu {
            padding: 15px 10px;
        }

        .sidebar-menu li {
            margin-bottom: 8px;
        }

        .sidebar-menu .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 12px 20px;
            border-radius: 12px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        .sidebar-menu .nav-link i {
            font-size: 1.1rem;
            margin-left: 12px; /* RTL margin */
        }

        .sidebar-menu .nav-link:hover, .sidebar-menu .nav-link:focus {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
            transform: translateX(-5px); /* حركة خفيفة لليسار في RTL */
        }

        .sidebar-menu .nav-link.active-link {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            border-right: 4px solid var(--gold-color);
            font-weight: 700;
        }

        .sidebar-menu .nav-link.active-link i {
            color: var(--gold-color);
        }

        /* ================= قسم المحتوى (Main Content) ================= */
        #content {
            width: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* ================= الشريط العلوي (Top Navbar) ================= */
        .top-navbar {
            background: #fff;
            padding: 15px 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            z-index: 1000;
        }

        .toggle-btn {
            background: var(--primary-color) !important;
            color: white !important;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }

        .toggle-btn:hover {
            background: var(--gold-color) !important;
            transform: scale(1.05);
        }

        /* أزرار ومكونات عامة */
        .btn-gold {
            background: linear-gradient(45deg, #d4af37, #e5c158);
            color: #fff;
            border: none;
        }
        .btn-gold:hover {
            background: linear-gradient(45deg, #c5a028, #d4af37);
            color: #fff;
        }

        /* استجابة الموبايل */
        @media (max-width: 768px) {
            #sidebar {
                margin-right: -260px;
                position: fixed;
                height: 100vh;
            }
            #sidebar.active {
                margin-right: 0;
            }
        }
    </style>
    <?php echo $__env->yieldContent('styles'); ?> 
</head>
<body>

    <div class="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header d-flex align-items-center justify-content-center gap-2">
                <i class="fas fa-chart-pie fa-2x text-warning"></i>
                <h4 class="mb-0 fw-bold">بيت المحاسبة</h4>
            </div>

            <ul class="list-unstyled sidebar-menu">
                <li>
                    <a href="<?php echo e(route('dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active-link' : ''); ?>">
                        <i class="fas fa-home fa-fw"></i> لوحة القيادة
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(route('clients.index')); ?>" class="nav-link <?php echo e(request()->routeIs('clients.*') ? 'active-link' : ''); ?>">
                        <i class="fas fa-users fa-fw"></i> إدارة العملاء
                    </a>
                </li>
                
                <?php if(auth()->check() && auth()->user()->role === 'admin'): ?>
                <li class="mt-3 mb-2 px-4 text-white-50 small fw-bold">صلاحيات الإدارة</li>
                <li>
                    <a href="<?php echo e(route('reports.index')); ?>" class="nav-link <?php echo e(request()->routeIs('reports.*') ? 'active-link' : ''); ?>">
                        <i class="fas fa-chart-line fa-fw text-warning"></i> التقارير المتقدمة
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(route('users.index')); ?>" class="nav-link <?php echo e(request()->routeIs('users.*') ? 'active-link' : ''); ?>">
                        <i class="fas fa-user-tie fa-fw"></i> إدارة الموظفين
                    </a>
                </li>
                <li>
                    <a href="<?php echo e(route('system_logs.index')); ?>" class="nav-link <?php echo e(request()->routeIs('system_logs.*') ? 'active-link' : ''); ?>">
                        <i class="fas fa-shield-alt fa-fw"></i> سجل الرقابة
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>

        <div id="content" class="bg-light">
            
            <nav class="navbar top-navbar d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" id="sidebarCollapse" class="btn toggle-btn shadow-sm">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h5 class="mb-0 fw-bold text-dark d-none d-md-block"><?php echo $__env->yieldContent('title', 'مرحباً بك'); ?></h5>
                </div>

                <div class="d-flex align-items-center gap-3">
                    
                    <?php if(auth()->guard()->check()): ?>
                    <div class="dropdown mx-2">
                        <a class="btn btn-light border-0 bg-transparent position-relative text-dark shadow-none p-2 d-flex align-items-center justify-content-center" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px; border-radius: 12px;">
                            <i class="fas fa-bell fs-5"></i>
                            <span id="notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none shadow-sm" style="font-size: 0.65rem; margin-top: 5px; margin-left: -5px;">0</span>
                        </a>
                        <div class="dropdown-menu shadow-lg border-0 rounded-4 p-0 mt-2" aria-labelledby="notifDropdown" style="width: 350px; max-height: 500px; left: 0 !important; right: auto !important; position: absolute; overflow: hidden;">
                            
                            <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-light rounded-top-4">
                                <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-bell text-primary me-2"></i> الإشعارات</h6>
                                <button onclick="markAllNotifsRead()" class="btn btn-sm btn-link text-decoration-none p-0 fw-bold" style="font-size: 0.8rem;">تحديد كـ مقروء</button>
                            </div>

                            <div class="d-flex border-bottom bg-white" style="overflow-x: auto; white-space: nowrap;" id="notif-tabs">
                                <button class="btn btn-sm btn-light active flex-fill rounded-0 fw-bold border-0 border-bottom border-primary border-2 notif-tab-btn" data-type="all" onclick="filterNotifs('all', this)">الكل</button>
                                <button class="btn btn-sm btn-light flex-fill rounded-0 text-muted border-0 notif-tab-btn" data-type="task" onclick="filterNotifs('task', this)">المهام</button>
                                <button class="btn btn-sm btn-light flex-fill rounded-0 text-muted border-0 notif-tab-btn" data-type="chat" onclick="filterNotifs('chat', this)">الشات</button>
                                <button class="btn btn-sm btn-light flex-fill rounded-0 text-muted border-0 notif-tab-btn" data-type="system" onclick="filterNotifs('system', this)">النظام</button>
                            </div>

                            <div id="notif-list" class="p-0 bg-white rounded-bottom-4" style="overflow-y: auto; max-height: 350px; min-height: 120px;">
                                <div class="text-center p-4 text-muted small"><i class="fas fa-spinner fa-spin mb-2 fs-4"></i><br>جاري التحميل...</div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="dropdown">
                        <button class="btn btn-light border-0 bg-transparent d-flex align-items-center gap-2 dropdown-toggle shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 35px; height: 35px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="fw-bold text-dark"><?php echo e(auth()->user()->username ?? 'مستخدم'); ?></span>
                        </button>
                        <ul class="dropdown-menu shadow-lg border-0 rounded-4 p-2" style="min-width: 200px; left: 0 !important; right: auto !important; position: absolute;">
                            <li>
                                <a class="dropdown-item py-2 fw-bold text-secondary rounded" href="<?php echo e(route('profile.index')); ?>">
                                    <i class="fas fa-cog me-2"></i> إعدادات الحساب
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="<?php echo e(route('logout')); ?>" class="m-0">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="dropdown-item py-2 fw-bold text-danger rounded">
                                        <i class="fas fa-sign-out-alt me-2"></i> تسجيل الخروج
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="container-fluid py-4 px-lg-4">
                <?php echo $__env->yieldContent('content'); ?>
            </main>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('sidebarCollapse').addEventListener('click', function() {
                document.getElementById('sidebar').classList.toggle('active');
            });
        });
    </script>

    <?php echo $__env->yieldContent('scripts'); ?> 

    <?php if(session('success')): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'عمل رائع!',
            text: "<?php echo e(session('success')); ?>",
            timer: 3000,
            showConfirmButton: false,
            customClass: { popup: 'rounded-4 shadow-lg border-0' }
        });
    </script>
    <?php endif; ?>

<?php if(auth()->check() && auth()->user()->role === 'admin'): ?>
<div class="modal fade" id="addClientModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-light border-0 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-plus text-primary me-2"></i> إضافة عميل جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('clients.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">اسم العميل أو الشركة</label>
                            <input type="text" name="name" class="form-control shadow-sm border-light" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">حالة العميل</label>
                            <select name="status" class="form-select shadow-sm border-light" required>
                                <option value="active" selected>نشط</option>
                                <option value="inactive">معطل</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">نوع الباقة</label>
                            <select name="package_type" class="form-select shadow-sm" required>
                                <option value="basic">أساسية</option>
                                <option value="advanced">متقدمة</option>
                                <option value="professional">احترافية</option>
                                <option value="comprehensive">شاملة</option>
                                <option value="custom" class="fw-bold text-primary">مخصصة</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-success">قيمة الاشتراك (بالريال)</label>
                            <input type="number" step="0.01" name="subscription_amount" class="form-control shadow-sm border-success" placeholder="مثال: 1500.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">الرقم الضريبي</label>
                            <input type="text" name="tax_number" class="form-control shadow-sm border-light" placeholder="مثال: 300123456789123">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">السجل التجاري</label>
                            <input type="text" name="commercial_register" class="form-control shadow-sm border-light" placeholder="مثال: 1010123456">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">بداية الاشتراك</label>
                            <input type="date" name="sub_start_date" id="add_start_date" class="form-control shadow-sm border-light">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">مدة الاشتراك</label>
                            <select name="subscription_duration" id="add_duration" class="form-select shadow-sm border-light">
                                <option value="" disabled selected>اختر المدة...</option>
                                <option value="monthly">شهري</option>
                                <option value="quarterly">ربع سنوي (3 شهور)</option>
                                <option value="semi_annual">نصف سنوي (6 شهور)</option>
                                <option value="annual">سنوي</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-danger">تاريخ الانتهاء</label>
                            <input type="date" id="add_end_date" class="form-control shadow-sm border-light bg-light" readonly title="يتم حسابه تلقائياً">
                        </div>
                        
                        <div class="col-md-12 border-top pt-3 mt-3">
                            <label class="form-label fw-bold text-primary mb-3"><i class="fas fa-eye me-1"></i> نطاق رؤية العميل</label>
                            <div class="d-flex flex-wrap gap-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input visibility-radio" type="radio" name="visibility" id="vis_all_new" value="all" checked>
                                    <label class="form-check-label fw-bold text-success" for="vis_all_new">متاح للجميع</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input visibility-radio" type="radio" name="visibility" id="vis_specific_new" value="specific">
                                    <label class="form-check-label fw-bold text-primary" for="vis_specific_new">تخصيص لموظفين محددين</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input visibility-radio" type="radio" name="visibility" id="vis_admins_new" value="admins_only">
                                    <label class="form-check-label fw-bold text-danger" for="vis_admins_new">مغلق للإدارة فقط</label>
                                </div>
                            </div>

                            <div class="border rounded-3 p-3 bg-white shadow-sm employees-box" style="max-height: 150px; overflow-y: auto; display: none;">
                                <?php $__currentLoopData = \App\Models\User::where('role', '!=', 'admin')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="form-check mb-2 border-bottom pb-1">
                                        <input class="form-check-input" type="checkbox" name="assigned_users[]" value="<?php echo e($u->id); ?>" id="add_u_<?php echo e($u->id); ?>">
                                        <label class="form-check-label small text-dark fw-bold" for="add_u_<?php echo e($u->id); ?>"><?php echo e($u->username); ?></label>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">حفظ العميل</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addTaskModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-light border-0 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-thumbtack text-warning me-2"></i> إنشاء تكليف جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('tasks.store')); ?>" method="POST" id="addTaskFormId">
                <?php echo csrf_field(); ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">العميل المستهدف</label>
                            <select name="client_id" id="dynamic_client_select" class="form-select shadow-sm border-light" required>
                                <option value="" disabled selected>-- اختر العميل --</option>
                                <?php $__currentLoopData = \App\Models\Client::where('status', '!=', 'completed')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($c->id); ?>" <?php echo e((isset($client) && $client->id == $c->id) ? 'selected' : ''); ?>><?php echo e($c->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">موعد التسليم النهائى</label>
                            <input type="datetime-local" name="deadline" class="form-control shadow-sm border-light" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">وصف التكليف / تفاصيل المهمة</label>
                            <textarea name="task_desc" class="form-control shadow-sm border-light" rows="3" required placeholder="ما الذي يجب إنجازه؟ "></textarea>
                        </div>
                        <div class="col-md-12 mt-2">
                            <label class="form-label small fw-bold text-info"><i class="fas fa-link"></i> رابط مرفق مع المهمة (اختياري)</label>
                            <input type="url" name="attachment_url" class="form-control shadow-sm border-light text-start" dir="ltr" placeholder="https://...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">الأولوية</label>
                            <select name="priority" class="form-select shadow-sm border-light" required>
                                <option value="low">عادية</option>
                                <option value="medium" selected>متوسطة</option>
                                <option value="high">عاجلة جداً</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label small fw-bold"><i class="fas fa-sync-alt text-primary"></i> تكرار المهمة</label>
                            <select name="recurrence_type" id="recurrence_select" class="form-select shadow-sm border-light">
                                <option value="none" selected>مرة واحدة (لا تتكرر)</option>
                                <option value="daily">يومياً أوتوماتيك</option>
                                <option value="weekly">أسبوعياً أوتوماتيك</option>
                                <option value="monthly">شهرياً أوتوماتيك</option>
                            </select>
                        </div>
                        <div class="col-md-4" id="recurrence_end_div" style="display: none;">
                            <label class="form-label small fw-bold text-danger">إيقاف التكرار (اختياري)</label>
                            <input type="date" name="recurrence_end_date" class="form-control shadow-sm border-light" title="تاريخ انتهاء التكرار الدائم">
                        </div>

                        <div class="col-md-12 mt-3">
                            <label class="form-label small fw-bold text-primary">إسناد إلى الموظفين</label>
                            <div id="dynamic_users_container" class="border rounded-3 p-3 bg-white shadow-sm" style="max-height: 120px; overflow-y: auto;">
                                <div class="text-muted small text-center mt-2">الرجاء اختيار العميل أولاً.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-gold rounded-pill px-5 fw-bold shadow">اعتماد التكليف</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editTaskModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-light border-0 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit text-primary me-2"></i> تعديل بيانات المهمة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTaskForm" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">العميل المستهدف</label>
                            <select name="client_id" id="edit_task_client_id" class="form-select shadow-sm border-light" required>
                                <option value="" disabled>-- اختر العميل --</option>
                                <?php $__currentLoopData = \App\Models\Client::where('status', '!=', 'completed')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">موعد التسليم</label>
                            <input type="datetime-local" name="deadline" id="edit_task_deadline" class="form-control shadow-sm border-light" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">تفاصيل المهمة</label>
                            <textarea name="task_desc" id="edit_task_desc" class="form-control shadow-sm border-light" rows="4" required></textarea>
                        </div>
                        <div class="col-md-12 mt-2">
                            <label class="form-label small fw-bold text-info"><i class="fas fa-link"></i> رابط مرفق مع المهمة (اختياري)</label>
                            <input type="url" name="attachment_url" id="edit_task_attachment_url" class="form-control shadow-sm border-light text-start" dir="ltr">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">الأولوية</label>
                            <select name="priority" id="edit_task_priority" class="form-select shadow-sm border-light" required>
                                <option value="low">عادية</option>
                                <option value="medium">متوسطة</option>
                                <option value="high">عاجلة جداً</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold"><i class="fas fa-sync-alt text-primary"></i> تكرار المهمة</label>
                            <select name="recurrence_type" id="edit_recurrence_select" class="form-select shadow-sm border-light">
                                <option value="none">مرة واحدة (لا تتكرر)</option>
                                <option value="daily">يومياً أوتوماتيك</option>
                                <option value="weekly">أسبوعياً أوتوماتيك</option>
                                <option value="monthly">شهرياً أوتوماتيك</option>
                            </select>
                        </div>
                        <div class="col-md-4" id="edit_recurrence_end_div" style="display: none;">
                            <label class="form-label small fw-bold text-danger">إيقاف التكرار (اختياري)</label>
                            <input type="date" name="recurrence_end_date" id="edit_recurrence_end_date" class="form-control shadow-sm border-light">
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label small fw-bold text-primary">الموظفين المكلفين</label>
                            <div id="edit_dynamic_users_container" class="border rounded-3 p-3 bg-white shadow-sm" style="max-height: 120px; overflow-y: auto;">
                                <div class="text-muted small text-center mt-2">جاري التحميل...</div>
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
<?php endif; ?>

<?php if(auth()->guard()->check()): ?>
<div class="modal fade" id="completeTaskModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="completeTaskForm" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <?php echo csrf_field(); ?>
            <div class="modal-header bg-success text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-check-circle me-2"></i> تأكيد إنجاز المهمة</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">رد الإنجاز / تقرير العمل (اختياري)</label>
                    <textarea name="completion_reply" class="form-control shadow-sm" rows="3" placeholder="اكتب ما قمت بإنجازه، أو ضع رابطاً للملف الجديد هنا..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm" onclick="this.disabled=true; this.innerHTML='جاري التأكيد...'; this.form.submit();">تأكيد الإنجاز</button>
            </div>
        </form>
    </div>
</div>

<script>
    let allNotifications = []; 
    let currentTab = 'all';
    let currentUnreadCount = 0;

    function fetchNotifications() {
        fetch('<?php echo e(route("notifications.fetch")); ?>')
            .then(res => res.json())
            .then(data => {
                allNotifications = data.notifications;
                let newUnreadCount = data.unread_count;
                
                // 1. تحديث الجرس (Badge)
                const badge = document.getElementById('notif-badge');
                if (badge) {
                    if (newUnreadCount > 0) {
                        badge.innerText = newUnreadCount > 99 ? '99+' : newUnreadCount;
                        badge.classList.remove('d-none');
                        
                        // تأثير مرئي لو في إشعار جديد فعلاً
                        if (newUnreadCount > currentUnreadCount) {
                            badge.classList.add('animate__animated', 'animate__rubberBand');
                            setTimeout(() => badge.classList.remove('animate__animated', 'animate__rubberBand'), 1000);
                        }
                    } else {
                        badge.classList.add('d-none');
                    }
                }
                currentUnreadCount = newUnreadCount;

                // 2. تحديث قائمة الإشعارات فوراً
                renderNotifications();
            }).catch(err => console.error('Notifications Error:', err));
    }

    // منع إغلاق قائمة الإشعارات عند الضغط بداخلها
    document.querySelector('.dropdown-menu[aria-labelledby="notifDropdown"]')?.addEventListener('click', function (e) {
        e.stopPropagation();
    });

    function filterNotifs(type, btnElement) {
        currentTab = type;
        document.querySelectorAll('.notif-tab-btn').forEach(btn => {
            btn.classList.remove('active', 'border-bottom', 'border-primary', 'border-2', 'fw-bold');
            btn.classList.add('text-muted');
        });
        btnElement.classList.add('active', 'border-bottom', 'border-primary', 'border-2', 'fw-bold');
        btnElement.classList.remove('text-muted');
        renderNotifications();
    }

    function renderNotifications() {
        let listContainer = document.getElementById('notif-list');
        if(!listContainer) return;

        let filtered = currentTab === 'all' ? allNotifications : allNotifications.filter(n => n.data.type === currentTab);

        if(filtered.length === 0) {
            listContainer.innerHTML = '<div class="text-center p-5 text-muted small"><i class="far fa-bell-slash fs-2 mb-2 opacity-50"></i><br>لا توجد إشعارات هنا.</div>';
            return;
        }

        let html = '';
        filtered.forEach(notif => {
            let isUnread = notif.read_at === null;
            let bgClass = isUnread ? 'bg-primary bg-opacity-10' : 'bg-white';
            let icon = 'fa-bell text-secondary';
            if(notif.data.type === 'task') icon = 'fa-tasks text-success';
            if(notif.data.type === 'chat') icon = 'fa-comments text-primary';
            if(notif.data.type === 'system') icon = 'fa-exclamation-circle text-warning';

            html += `
                <div class="d-flex align-items-start p-3 border-bottom position-relative hover-zoom ${bgClass}" style="transition: 0.2s;">
                    <div class="me-3 mt-1"><i class="fas ${icon} fs-5"></i></div>
                    <div class="flex-grow-1" onclick="handleNotifClick('${notif.id}', '${notif.data.url}')" style="cursor: pointer;">
                        <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.85rem;">${notif.data.title}</h6>
                        <p class="mb-1 text-muted" style="font-size: 0.75rem;">${notif.data.body}</p>
                        <small class="text-muted" style="font-size: 0.65rem;" dir="ltr">${new Date(notif.created_at).toLocaleString('ar-EG')}</small>
                    </div>
                    <button onclick="deleteNotif('${notif.id}')" class="btn btn-sm btn-link text-danger p-0 position-absolute top-0 end-0 m-2 shadow-none" title="حذف الإشعار"><i class="fas fa-times"></i></button>
                </div>
            `;
        });
        listContainer.innerHTML = html;
    }

    function handleNotifClick(id, url) {
        fetch(`/notifications/read/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' } })
            .then(() => window.location.href = url);
    }

    function deleteNotif(id) {
        fetch(`/notifications/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' } })
            .then(() => fetchNotifications()); 
    }

    function markAllNotifsRead() {
        fetch('/notifications/read-all', { method: 'POST', headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' } })
            .then(() => fetchNotifications());
    }

    // تشغيل الجلب اللحظي كل 15 ثانية (Smart Polling)
    document.addEventListener("DOMContentLoaded", function() {
        fetchNotifications();
        setInterval(fetchNotifications, 15000); 
    });
</script>
<?php endif; ?>

<script>
    // ==========================================
    // سكريبتات عامة (المهام، العملاء، التواريخ)
    // ==========================================
    document.addEventListener('DOMContentLoaded', function() {
        
        // أزرار نطاق رؤية العميل
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

        // جلب موظفين العميل لتحديدهم في المهام
        const clientSelect = document.getElementById('dynamic_client_select');
        const usersContainer = document.getElementById('dynamic_users_container');

        function fetchUsersForClient(clientId) {
            if(!clientId) return;
            usersContainer.innerHTML = '<div class="text-center mt-3"><div class="spinner-border spinner-border-sm text-primary"></div> جاري التحميل...</div>';
            
            fetch('/clients/' + clientId + '/users')
                .then(response => response.json())
                .then(users => {
                    usersContainer.innerHTML = '';
                    if(users.length === 0) {
                        usersContainer.innerHTML = '<div class="text-danger small text-center mt-3">لا يوجد موظفين مخصصين لهذا العميل.</div>';
                        return;
                    }
                    
                    users.forEach(u => {
                        let isAdmin = u.role === 'admin' ? '<span class="text-danger small float-end">(أدمن)</span>' : '';
                        let html = `
                            <div class="form-check mb-2 border-bottom pb-1">
                                <input class="form-check-input" type="checkbox" name="assigned_to[]" value="${u.id}" id="dyn_user_${u.id}">
                                <label class="form-check-label fw-bold small text-dark w-100" style="cursor: pointer;" for="dyn_user_${u.id}">
                                    ${u.username} ${isAdmin}
                                </label>
                            </div>
                        `;
                        usersContainer.innerHTML += html;
                    });
                });
        }

        if(clientSelect) {
            clientSelect.addEventListener('change', function() { fetchUsersForClient(this.value); });
            if(clientSelect.value) { fetchUsersForClient(clientSelect.value); }
        }

        // حساب تاريخ الانتهاء التلقائي للعميل
        function calculateEndDate(startInputId, durationInputId, endInputId) {
            let startDate = document.getElementById(startInputId)?.value;
            let duration = document.getElementById(durationInputId)?.value;
            let endInput = document.getElementById(endInputId);
            
            if (startDate && duration && endInput) {
                let date = new Date(startDate);
                if (duration === 'monthly') date.setMonth(date.getMonth() + 1);
                else if (duration === 'quarterly') date.setMonth(date.getMonth() + 3);
                else if (duration === 'semi_annual') date.setMonth(date.getMonth() + 6);
                else if (duration === 'annual') date.setFullYear(date.getFullYear() + 1);
                
                endInput.value = date.toISOString().split('T')[0];
            } else if(endInput) {
                endInput.value = '';
            }
        }

        document.getElementById('add_start_date')?.addEventListener('change', () => calculateEndDate('add_start_date', 'add_duration', 'add_end_date'));
        document.getElementById('add_duration')?.addEventListener('change', () => calculateEndDate('add_start_date', 'add_duration', 'add_end_date'));
        document.getElementById('edit_start_date')?.addEventListener('change', () => calculateEndDate('edit_start_date', 'edit_duration', 'edit_end_date'));
        document.getElementById('edit_duration')?.addEventListener('change', () => calculateEndDate('edit_start_date', 'edit_duration', 'edit_end_date'));
    });

    // تكرار المهام إظهار/إخفاء التاريخ
    document.getElementById('recurrence_select')?.addEventListener('change', function() {
        document.getElementById('recurrence_end_div').style.display = this.value === 'none' ? 'none' : 'block';
    });
    document.getElementById('edit_recurrence_select')?.addEventListener('change', function() {
        document.getElementById('edit_recurrence_end_div').style.display = this.value === 'none' ? 'none' : 'block';
    });

    function openCompleteModal(taskId) {
        document.getElementById('completeTaskForm').action = '/tasks/' + taskId + '/complete';
        var completeModal = new bootstrap.Modal(document.getElementById('completeTaskModal'));
        completeModal.show();
    }

    function openEditTaskModal(btn) {
        let id = btn.getAttribute('data-id');
        let desc = btn.getAttribute('data-desc');
        let deadline = btn.getAttribute('data-deadline');
        let priority = btn.getAttribute('data-priority');
        let clientId = btn.getAttribute('data-client');
        let recurrence = btn.getAttribute('data-recurrence');
        let recurrenceEnd = btn.getAttribute('data-recurrence-end');
        let users = JSON.parse(btn.getAttribute('data-users'));
        let attachment = btn.getAttribute('data-attachment') || ''; 

        document.getElementById('editTaskForm').action = '/tasks/' + id;
        document.getElementById('edit_task_desc').value = desc;
        document.getElementById('edit_task_attachment_url').value = attachment; 
        document.getElementById('edit_task_priority').value = priority;
        document.getElementById('edit_task_client_id').value = clientId;
        document.getElementById('edit_recurrence_select').value = recurrence || 'none';

        if (recurrence && recurrence !== 'none') {
            document.getElementById('edit_recurrence_end_div').style.display = 'block';
            document.getElementById('edit_recurrence_end_date').value = recurrenceEnd ? recurrenceEnd.split(' ')[0] : '';
        } else {
            document.getElementById('edit_recurrence_end_div').style.display = 'none';
            document.getElementById('edit_recurrence_end_date').value = '';
        }

        if (deadline) {
            let dateObj = new Date(deadline);
            dateObj.setMinutes(dateObj.getMinutes() - dateObj.getTimezoneOffset());
            document.getElementById('edit_task_deadline').value = dateObj.toISOString().slice(0, 16);
        }

        fetchUsersForEdit(clientId, users);
        var editModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
        editModal.show();
    }

    function fetchUsersForEdit(clientId, selectedUsers) {
        const usersContainer = document.getElementById('edit_dynamic_users_container');
        if(!clientId) return;
        usersContainer.innerHTML = '<div class="text-center mt-3"><div class="spinner-border spinner-border-sm text-primary"></div> جاري التحميل...</div>';

        fetch('/clients/' + clientId + '/users')
            .then(response => response.json())
            .then(users => {
                usersContainer.innerHTML = '';
                if(users.length === 0) {
                    usersContainer.innerHTML = '<div class="text-danger small text-center mt-3">لا يوجد موظفين مخصصين لهذا العميل.</div>';
                    return;
                }

                users.forEach(u => {
                    let isAdmin = u.role === 'admin' ? '<span class="text-danger small float-end">(أدمن)</span>' : '';
                    let isChecked = selectedUsers.includes(u.id) ? 'checked' : '';
                    let html = `
                        <div class="form-check mb-2 border-bottom pb-1">
                            <input class="form-check-input" type="checkbox" name="assigned_to[]" value="${u.id}" id="edit_dyn_user_${u.id}" ${isChecked}>
                            <label class="form-check-label fw-bold small text-dark w-100" style="cursor: pointer;" for="edit_dyn_user_${u.id}">
                                ${u.username} ${isAdmin}
                            </label>
                        </div>
                    `;
                    usersContainer.innerHTML += html;
                });
            });
    }

    document.getElementById('edit_task_client_id')?.addEventListener('change', function() {
        fetchUsersForEdit(this.value, []);
    });

    document.getElementById('addTaskFormId')?.addEventListener('submit', function(e) {
        let checkedUsers = document.querySelectorAll('input[name="assigned_to[]"]:checked');
        if(checkedUsers.length === 0) {
            e.preventDefault(); 
            Swal.fire({
                icon: 'warning',
                title: 'تنبيه هام!',
                text: 'لا يمكنك إنشاء تكليف بدون اختيار موظف واحد على الأقل!',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#d33'
            });
        }
    });

    function confirmAction(event, formElement, message, iconType = 'warning', confirmColor = '#3085d6') {
        event.preventDefault(); 
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
                formElement.submit(); 
            }
        });
    }
</script>
</body>
</html><?php /**PATH E:\crm\accounting-crm\resources\views/layouts/app.blade.php ENDPATH**/ ?>