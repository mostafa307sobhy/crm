@extends('layouts.app')

@section('title', 'الرئيسية')

@section('content')
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
        @if(auth()->user()->role === 'admin')
        <button data-bs-toggle="modal" data-bs-target="#addClientModal" class="btn btn-outline-primary fw-bold rounded-pill shadow-sm me-2">
            <i class="fas fa-user-plus"></i> إضافة عميل جديد
        </button>
        <button data-bs-toggle="modal" data-bs-target="#addTaskModal" class="btn btn-gold fw-bold rounded-pill shadow-sm">
            <i class="fas fa-plus"></i> إضافة مهمة سريعة
        </button>
        @endif
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 text-center p-3" style="background: linear-gradient(45deg, #198754, #20c997); color: white;">
            <i class="fas fa-user-tie fa-2x mb-2 opacity-75"></i>
            <h6 class="fw-bold mt-2">{{ $isAdmin ? 'إجمالي العملاء' : 'عملائي المخصصين' }}</h6>
            <h3 class="fw-bold mb-0">{{ $activeClientsCount }}</h3>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 text-center p-3" style="background: linear-gradient(45deg, #0d6efd, #0dcaf0); color: white;">
            <i class="fas fa-tasks fa-2x mb-2 opacity-75"></i>
            <h6 class="fw-bold mt-2">{{ $isAdmin ? 'إجمالي المعلقة' : 'مهامي المعلقة' }}</h6>
            <h3 class="fw-bold mb-0">{{ $pendingTasksCount }}</h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 text-center p-3" style="background: linear-gradient(45deg, #6c757d, #adb5bd); color: white;">
            <i class="fas fa-list-ol fa-2x mb-2 opacity-75"></i>
            <h6 class="fw-bold mt-2">{{ $isAdmin ? 'إجمالي المهام' : 'إجمالي مهامي' }}</h6>
            <h3 class="fw-bold mb-0">{{ $totalTasksCount }}</h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 text-center p-3" style="background: linear-gradient(45deg, #dc3545, #f87171); color: white;">
            <i class="fas fa-check-double fa-2x mb-2 opacity-75"></i>
            <h6 class="fw-bold mt-2">{{ $isAdmin ? 'المهام المنجزة' : 'مهامي المنجزة' }}</h6>
            <h3 class="fw-bold mb-0">{{ $completedTasksCount }}</h3>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('dashboard') }}" class="card border-0 shadow-sm rounded-4 mt-2 mb-4 p-3 bg-white">
    <div class="row g-2 align-items-end">
        <div class="col-md-2">
            <label class="small fw-bold text-muted mb-1"><i class="fas fa-search text-info"></i> بحث بكلمة</label>
            <input type="text" name="search_text" value="{{ request('search_text') }}" class="form-control shadow-sm border-light" placeholder="ابحث في التفاصيل...">
        </div>
        <div class="col-md-2">
            <label class="small fw-bold text-muted mb-1"><i class="fas fa-building"></i> العميل</label>
            <select name="client_id" class="form-select shadow-sm border-light">
                <option value="">-- الكل --</option>
                @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ Str::limit($c->name, 20) }}</option>
                @endforeach
            </select>
        </div>
        
        @if($isAdmin)
        <div class="col-md-2">
            <label class="small fw-bold text-muted mb-1"><i class="fas fa-user-tie"></i> المكلف</label>
            <select name="user_id" class="form-select shadow-sm border-light">
                <option value="">-- الكل --</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->username }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="col-md-2">
            <label class="small fw-bold text-muted mb-1"><i class="fas fa-calendar-alt text-primary"></i> من تاريخ</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control shadow-sm border-light">
        </div>
        <div class="col-md-2">
            <label class="small fw-bold text-muted mb-1"><i class="fas fa-calendar-check text-danger"></i> إلى تاريخ</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control shadow-sm border-light">
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-primary flex-grow-1 fw-bold shadow-sm"><i class="fas fa-search"></i></button>
            <a href="{{ route('dashboard') }}" class="btn btn-light shadow-sm border" title="إلغاء الفلتر"><i class="fas fa-times text-danger"></i></a>
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
                            @forelse($pendingTasksList as $task)
                                <tr class="{{ \Carbon\Carbon::parse($task->deadline)->isPast() ? 'table-danger border-danger' : '' }}">
                                    <td class="text-start px-3 fw-bold text-dark" style="white-space: pre-line; line-height: 1.3; font-size: 0.85rem;">
                                        {{ Str::limit($task->task_desc, 100) }}
                                        @if($task->recurrence_type != 'none')
                                            <span class="badge bg-light text-primary border ms-1" title="مهمة متكررة"><i class="fas fa-sync-alt"></i> دورية</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('clients.show', $task->client_id) }}" class="text-decoration-none" title="الذهاب لملف العميل">
                                            <span class="badge bg-light text-dark border p-2" style="transition: 0.3s;"><i class="fas fa-building text-primary me-1"></i> {{ $task->client->name ?? 'غير محدد' }}</span>
                                        </a>
                                    </td>
                                    <td style="min-width: 240px;">
                                        @php
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
                                        @endphp

                                        <div class="border rounded-3 p-1 bg-white mx-auto shadow-sm" style="max-width: 230px; font-size: 0.75rem;">
                                            <div class="d-flex justify-content-between align-items-center px-1">
                                                <span class="text-muted">الطلب: <i class="fas fa-clock text-secondary ms-1"></i></span>
                                                <span class="fw-bold text-dark" dir="ltr">{{ $createdAt->format('Y-m-d h:i A') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-1 px-1">
                                                <span class="text-muted">التسليم: <i class="fas fa-bullseye text-secondary ms-1"></i></span>
                                                <span class="fw-bold text-primary" dir="ltr">{{ $deadline->format('Y-m-d h:i A') }}</span>
                                            </div>
                                            <div class="text-center">
                                                <span class="badge rounded-pill {{ $badgeClass }} px-2 py-1 shadow-sm" style="font-size: 0.7rem; {{ $badgeStyle ?? '' }}">
                                                    {{ $statusText }} <i class="{{ $icon }} ms-1"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($task->priority == 'high') <span class="badge bg-danger rounded-pill">عالية</span>
                                        @elseif($task->priority == 'medium') <span class="badge bg-warning text-dark rounded-pill">متوسطة</span>
                                        @else <span class="badge bg-info text-dark rounded-pill">عادية</span> @endif
                                    </td>
                                    <td>
                                        @foreach($task->assignedUsers as $assignedUser)
                                            <span class="badge bg-secondary mb-1">{{ $assignedUser->username }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <form action="{{ route('tasks.complete', $task->id) }}" method="POST" onsubmit="confirmAction(event, this, 'هل تريد بالفعل إنجاز هذه المهمة؟', 'question', '#198754');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success rounded-circle d-flex justify-content-center align-items-center" title="إنجاز" style="width: 32px; height: 32px; padding: 0;">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            
                                            @if(auth()->user()->role === 'admin')
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-circle d-flex justify-content-center align-items-center" title="تعديل المهمة" style="width: 32px; height: 32px; padding: 0;"
                                                data-id="{{ $task->id }}"
                                                data-desc="{{ $task->task_desc }}"
                                                data-deadline="{{ $task->deadline }}"
                                                data-priority="{{ $task->priority }}"
                                                data-client="{{ $task->client_id }}"
                                                data-recurrence="{{ $task->recurrence_type }}"
                                                data-recurrence-end="{{ $task->recurrence_end_date }}"
                                                data-users="{{ json_encode($task->assignedUsers->pluck('id')) }}"
                                                onclick="openEditTaskModal(this)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="confirmAction(event, this, 'هل أنت متأكد من حذف هذه المهمة نهائياً؟ لا يمكن التراجع عن هذا الإجراء!', 'error', '#dc3545');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle d-flex justify-content-center align-items-center" title="حذف" style="width: 32px; height: 32px; padding: 0;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4 fw-bold">لا توجد مهام معلقة.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-4" dir="ltr">
                        {{ $pendingTasksList->withQueryString()->links() }}
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
                            @forelse($completedTasksList as $task)
                                <tr class="table-light opacity-75">
                                    <td class="text-start px-3 fw-bold text-muted text-decoration-line-through" style="white-space: pre-line; line-height: 1.3; font-size: 0.85rem;">
                                        {{ Str::limit($task->task_desc, 100) }}
                                    </td>
                                    <td>
                                        <a href="{{ route('clients.show', $task->client_id) }}" class="text-decoration-none" title="الذهاب لملف العميل">
                                            <span class="badge bg-light text-dark border p-2" style="transition: 0.3s;"><i class="fas fa-building text-primary me-1"></i> {{ $task->client->name ?? 'غير محدد' }}</span>
                                        </a>
                                    </td>
                                    <td style="min-width: 240px;">
                                        @php
                                            $deadline = \Carbon\Carbon::parse($task->deadline);
                                            $completedAt = $task->completed_at ? \Carbon\Carbon::parse($task->completed_at) : $task->updated_at;
                                            
                                            $wasLate = $completedAt->greaterThan($deadline);
                                            
                                            $badgeClass = $wasLate ? 'bg-danger text-white' : 'bg-success text-white';
                                            $icon = $wasLate ? 'fas fa-exclamation-triangle' : 'fas fa-check-double';
                                            $statusText = $wasLate ? "أُنجزت متأخرة" : "أُنجزت في الموعد";
                                        @endphp

                                        <div class="border rounded-3 p-1 bg-light shadow-sm mx-auto" style="max-width: 230px; font-size: 0.75rem;">
                                            <div class="d-flex justify-content-between align-items-center px-1">
                                                <span class="text-muted">الموعد: <i class="fas fa-bullseye text-secondary ms-1"></i></span>
                                                <span class="fw-bold text-dark" dir="ltr">{{ $deadline->format('Y-m-d h:i A') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-1 px-1">
                                                <span class="text-muted">الإنجاز: <i class="fas fa-flag-checkered text-success ms-1"></i></span>
                                                <span class="fw-bold text-success" dir="ltr">{{ $completedAt->format('Y-m-d h:i A') }}</span>
                                            </div>
                                            <div class="text-center">
                                                <span class="badge rounded-pill {{ $badgeClass }} px-2 py-1 shadow-sm" style="font-size: 0.7rem;">
                                                    {{ $statusText }} <i class="{{ $icon }} ms-1"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @foreach($task->assignedUsers as $assignedUser)
                                            <span class="badge bg-secondary mb-1">{{ $assignedUser->username }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if(auth()->user()->role === 'admin')
                                        <div class="d-flex justify-content-center gap-2">
                                            <form action="{{ route('tasks.undo', $task->id) }}" method="POST" onsubmit="confirmAction(event, this, 'هل تريد التراجع عن الإنجاز وإعادتها لقائمة المهام المعلقة؟', 'warning', '#ffc107');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning rounded-circle d-flex justify-content-center align-items-center" title="تراجع" style="width: 32px; height: 32px; padding: 0;">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="confirmAction(event, this, 'هل أنت متأكد من حذف هذه المهمة نهائياً؟ لا يمكن التراجع عن هذا الإجراء!', 'error', '#dc3545');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle d-flex justify-content-center align-items-center" title="حذف" style="width: 32px; height: 32px; padding: 0;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                        @else
                                            <span class="badge bg-success rounded-pill px-3"><i class="fas fa-check-double"></i> منجزة</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4 fw-bold">لا توجد مهام منجزة.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-4" dir="ltr">
                        {{ $completedTasksList->withQueryString()->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
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
@endsection