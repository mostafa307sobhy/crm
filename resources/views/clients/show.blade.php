@extends('layouts.app')

@section('title', $isWorkspace ? 'مساحة العمل الداخلية' : 'ملف العميل: ' . $client->name)

@section('content')
<style>
    :root { 
        --primary-color: #002E5D; 
        --gold-color: #CFB065; 
        --bg-color: #f8f9fa; 
        --chat-primary-msg: #E1EDFF; 
    }
    
    body { background-color: var(--bg-color); }
    .crm-header { 
        background: linear-gradient(135deg, var(--primary-color), #001f3f); 
        color: white; 
        padding: 25px 0; 
        border-bottom: 5px solid var(--gold-color); 
        border-radius: 16px; 
    }
    div.workspace-header { 
        background: linear-gradient(135deg, #1a1a1a, #333); 
        border-bottom: 5px solid var(--gold-color); 
    }
    
    .custom-card { 
        border: none; border-radius: 20px; 
        box-shadow: 0 8px 24px rgba(0,0,0,0.03); 
        background: #fff; overflow: hidden; 
    }
    .custom-card-header { background-color: rgba(255,255,255,0.9); border-bottom: 1px solid #f1f2f5; padding: 20px 24px; }
    
    .icon-box-sm { 
        width: 32px; height: 32px; 
        display: inline-flex; align-items: center; justify-content: center; 
        border-radius: 8px; font-size: 14px;
    }
    
    .nav-line-tabs { border-bottom: 2px solid #e9ecef; gap: 10px; }
    .nav-line-tabs .nav-link { color: #6c757d; font-weight: 700; border: none; border-bottom: 3px solid transparent; border-radius: 0; padding: 12px 20px; background: transparent; transition: all 0.3s ease; }
    .nav-line-tabs .nav-link.active { color: var(--primary-color); border-bottom: 3px solid var(--primary-color); background: transparent; box-shadow: none; }
    .nav-line-tabs .nav-link:hover:not(.active) { color: var(--primary-color); border-bottom: 3px solid #d1d1d1; }

    .tab-content > .active {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 250px);
    }

    .chat-card {
        height: 100% !important; 
        border: none;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .chat-container { 
        background: #fdfdfd; 
        overflow-y: auto; 
        display: flex; 
        flex-direction: column; 
        gap: 12px; 
        scroll-behavior: smooth; 
        padding-bottom: 20px;
    }
    
    .chat-container::-webkit-scrollbar { width: 5px; }
    .chat-container::-webkit-scrollbar-track { background: transparent; }
    .chat-container::-webkit-scrollbar-thumb { background: #e0e0e0; border-radius: 10px; }
    
    .hover-zoom:hover { transform: scale(1.03); transition: 0.1s; }
    
    .avatar { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #fff; font-size: 13px; flex-shrink: 0; }
    .bg-avatar-mine { background: linear-gradient(135deg, var(--gold-color), #b5954a); }
    .bg-avatar-other { background: linear-gradient(135deg, #6c757d, #495057); }

    .chat-message { max-width: 85%; }
    .chat-bubble { 
        padding: 12px 18px; 
        border-radius: 18px; 
        border-top-right-radius: 4px !important; 
        position: relative; 
        width: 100%; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.02); 
    }
    
    .bg-msg-mine { background: linear-gradient(135deg, #004c99, var(--primary-color)); color: white; }
    .bg-msg-mine .text-muted { color: rgba(255,255,255,0.6) !important; }
    .bg-msg-mine .chat-content { color: #fff; font-weight: 500; }
    .bg-msg-other { background: #f1f3f5; color: #333; border: 1px solid #e9ecef; }

    .chat-date-separator {
        display: flex; align-items: center; text-align: center; color: #adb5bd;
        font-weight: bold; font-size: 0.75rem; margin: 15px 0;
    }
    .chat-date-separator::before, .chat-date-separator::after { content: ''; flex: 1; border-bottom: 1px solid #e9ecef; }
    .chat-date-separator:not(:empty)::before { margin-left: 1rem; }
    .chat-date-separator:not(:empty)::after { margin-right: 1rem; }

    .system-msg-line { font-size: 0.8rem; background: #e9ecef; color: #6c757d; padding: 6px 18px; border-radius: 50rem; display: inline-block; font-weight: 600; border: 1px solid #dee2e6; }

    .chat-input-area { background: #f8f9fa; border-top: 1px solid #eee; }
    .chat-input-box { background: #fff; border-radius: 20px; border: 1px solid #e0e0e0; transition: 0.2s; padding: 6px; }
    .chat-input-box:focus-within { border-color: #99c2ff; box-shadow: 0 0 0 3px rgba(0, 76, 153, 0.05); }
    
    .chat-textarea {
        border: none !important; background: transparent !important; box-shadow: none !important;
        resize: none; max-height: 150px; font-size: 1rem; padding-top: 10px !important; padding-right: 15px !important; height: 42px;
    }
    .chat-textarea::-webkit-scrollbar { width: 3px; }
    .chat-textarea::-webkit-scrollbar-thumb { background: #eee; border-radius: 10px; }

    .highlight-msg { animation: highlight 2.5s ease-out; }
    @keyframes highlight { 0% { background-color: rgba(207, 176, 101, 0.5); transform: scale(1.02); } 100% { background-color: transparent; transform: scale(1); } }
</style>

@php
    $statusBadges = [
        'lead' => ['text' => 'عميل محتمل', 'class' => 'bg-secondary'],
        'setup' => ['text' => 'جاري التأسيس', 'class' => 'bg-warning text-dark'],
        'active' => ['text' => 'عميل نشط', 'class' => 'bg-success'],
        'inactive' => ['text' => 'معطل/مؤرشف', 'class' => 'bg-danger'],
        'completed' => ['text' => 'مكتمل/مغلق', 'class' => 'bg-info text-dark'],
    ];
    $curr_badge = $statusBadges[$client->status] ?? ['text' => 'غير محدد', 'class' => 'bg-secondary'];
    $packages_ar = ['basic' => 'باقة أساسية', 'advanced' => 'باقة متقدمة', 'professional' => 'باقة محترفة', 'comprehensive' => 'باقة شاملة', 'custom' => 'باقة مخصصة'];
    $client_package = $packages_ar[$client->package_type] ?? 'غير محدد';
    $isAdminAuth = auth()->user()->role === 'admin';
@endphp

<div class="crm-header mb-4 {{ $isWorkspace ? 'workspace-header' : '' }} shadow">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div>
                <h3 class="mb-0 fw-bold d-flex align-items-center">
                    @if($isWorkspace)
                        <div class="bg-white bg-opacity-10 p-2 rounded-3 me-3"><i class="fas fa-users-cog text-warning"></i></div> مساحة عمل الفريق
                    @else
                        <div class="bg-white bg-opacity-10 p-2 rounded-3 me-3"><i class="fas fa-building text-warning"></i></div> {{ $client->name }}
                        @if($isAdminAuth)
                            <button class="btn btn-sm btn-light rounded-circle text-primary ms-3 shadow-sm hover-zoom" data-bs-toggle="modal" data-bs-target="#editClientModal" title="تعديل العميل" style="width:36px; height:36px;">
                                <i class="fas fa-pen"></i>
                            </button>
                        @endif
                    @endif
                </h3>
            </div>
            @if(!$isWorkspace)
            <div class="ms-lg-4">
                <span class="badge {{ $curr_badge['class'] }} rounded-pill px-4 py-2 fs-6 shadow-sm border border-white border-opacity-25">{{ $curr_badge['text'] }}</span>
            </div>
            @endif
        </div>
        <div>
            <a href="{{ route('clients.index') }}" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm text-primary">
                <i class="fas fa-arrow-right me-2"></i> القائمة
            </a>
        </div>
    </div>
</div>

<ul class="nav nav-line-tabs mb-4 bg-white px-3 pt-2 rounded-4 shadow-sm align-items-center flex-nowrap overflow-auto" id="clientTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#overview" type="button"><i class="fas fa-info-circle me-1"></i> نظرة عامة</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tasks" type="button"><i class="fas fa-tasks me-1"></i> المهام المعلقة</button>
    </li>
    @if($isAdminAuth)
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#documents" type="button"><i class="fab fa-google-drive me-1"></i> الوثائق</button>
    </li>
    @endif
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#chat" type="button"><i class="fas fa-comments me-1"></i> سجل المتابعة والشات</button>
    </li>
</ul>

<div class="tab-content" id="clientTabsContent">
    
    <div class="tab-pane fade show active" id="overview" role="tabpanel">
        <div class="row g-4">
            
            @if($isAdminAuth && !$isWorkspace)
            <div class="col-lg-6">
                <div class="custom-card h-100 border-top border-primary border-4">
                    <div class="card-body p-0">
                        <div class="p-4 bg-light text-center border-bottom">
                            <h6 class="fw-bold text-muted mb-1">قيمة الاشتراك</h6>
                            <h3 class="fw-bold text-success mb-0">{{ $client->subscription_amount ? number_format($client->subscription_amount, 2) . ' ريال' : 'غير محدد' }}</h3>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                <span class="text-muted small fw-bold"><div class="icon-box-sm bg-primary bg-opacity-10 text-primary me-2"><i class="fas fa-box"></i></div> الباقة:</span>
                                <span class="fw-bold text-dark">{{ $client_package }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                <span class="text-muted small fw-bold"><div class="icon-box-sm bg-secondary bg-opacity-10 text-secondary me-2"><i class="fas fa-hashtag"></i></div> الضريبي:</span>
                                <span class="fw-bold text-dark">{{ $client->tax_number ?: '---' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                <span class="text-muted small fw-bold"><div class="icon-box-sm bg-secondary bg-opacity-10 text-secondary me-2"><i class="fas fa-certificate"></i></div> السجل:</span>
                                <span class="fw-bold text-dark">{{ $client->commercial_register ?: '---' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                <span class="text-muted small fw-bold"><div class="icon-box-sm bg-info bg-opacity-10 text-info me-2"><i class="fas fa-calendar-check"></i></div> البداية:</span>
                                <span class="fw-bold text-dark" dir="ltr">{{ $client->sub_start_date ?? '---' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                <span class="text-muted small fw-bold"><div class="icon-box-sm bg-danger bg-opacity-10 text-danger me-2"><i class="fas fa-calendar-times"></i></div> الانتهاء:</span>
                                <span class="fw-bold text-danger" dir="ltr">{{ $client->sub_end_date ?? '---' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <div class="{{ $isAdminAuth && !$isWorkspace ? 'col-lg-6' : 'col-12' }}">
                
                @php
                    $visibleNotes = $isAdminAuth ? $clientNotes : $clientNotes->where('type', 'alert');
                @endphp

                @if($visibleNotes->count() > 0 || $isAdminAuth) <div class="custom-card mb-4 border-top border-info border-4">
                    <div class="custom-card-header d-flex justify-content-between align-items-center py-3">
                        <h6 class="fw-bold mb-0 text-info"><i class="fas fa-exclamation-circle me-2"></i> {{ $isAdminAuth ? 'ملاحظات وتنبيهات' : 'تنبيهات هامة' }}</h6>
                        @if($isAdminAuth)
                        <button class="btn btn-sm btn-info text-white rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addNoteModal"><i class="fas fa-plus"></i> إضافة</button>
                        @endif
                    </div>
                    <div class="card-body p-3" style="max-height: 250px; overflow-y: auto;">
                        @forelse($visibleNotes as $note)
                            @php 
                                $isAlert = $note->type === 'alert';
                                $bg = $isAlert ? 'bg-danger bg-gradient text-white' : 'bg-light border';
                                $icon = $isAlert ? 'fa-exclamation-triangle' : 'fa-sticky-note text-warning';
                                $title = $isAlert ? 'تنبيه هام' : 'ملاحظة سرية';
                            @endphp
                            <div class="p-3 mb-2 rounded-4 {{ $bg }} shadow-sm">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="small fw-bold"><i class="fas {{ $icon }} me-1"></i> {{ $title }}</strong>
                                    <div class="d-flex align-items-center gap-2">
                                        <small class="{{ $isAlert ? 'text-white-50' : 'text-muted' }}" style="font-size:0.7rem;">{{ $note->user->username ?? '---' }}</small>
                                        @if($isAdminAuth)
                                        <button type="button" onclick="openEditNoteModal({{ $note->id }}, '{{ $note->type }}', `{{ htmlspecialchars($note->content, ENT_QUOTES) }}`)" class="btn btn-sm p-0 text-{{ $isAlert ? 'white' : 'primary' }} hover-zoom" title="تعديل"><i class="fas fa-edit"></i></button>
                                        <form action="{{ route('notes.destroy', $note->id) }}" method="POST" class="m-0 p-0 d-inline">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-sm p-0 text-{{ $isAlert ? 'white' : 'danger' }} hover-zoom" onclick="return confirmAction(event, this.closest('form'), 'سيتم حذف هذه الملاحظة نهائياً؟')" title="حذف"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                                <p class="mb-0 small" style="line-height: 1.6;">{!! nl2br(e($note->content)) !!}</p>
                            </div>
                        @empty
                            <div class="text-center text-muted opacity-50 py-4">
                                <i class="far fa-sticky-note fa-3x mb-3 text-info"></i>
                                <h6 class="fw-bold text-dark">لا توجد ملاحظات أو تنبيهات.</h6>
                                <p class="small mb-0">اضغط على زر (إضافة) لكتابة أول ملاحظة سرية للعميل.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                @endif

                @if($isAdminAuth)
                <div class="custom-card border-top border-warning border-4">
                    <div class="custom-card-header d-flex justify-content-between align-items-center py-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-address-book text-warning me-2"></i> جهات الاتصال</h6>
                        <button class="btn btn-sm btn-warning text-dark rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#addContactModal"><i class="fas fa-plus"></i></button>
                    </div>
                    <div class="card-body p-3" style="max-height: 250px; overflow-y: auto;">
                        @forelse($client->contacts as $contact)
                            <div class="bg-light p-3 rounded-4 border border-warning border-opacity-25 mb-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="d-block fw-bold text-dark small">{{ $contact->name }} <span class="badge bg-white text-muted border ms-1">{{ $contact->job_title }}</span></span>
                                    <small class="text-muted fw-bold d-block mt-1" dir="ltr">{{ $contact->phone }}</small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="tel:{{ $contact->phone }}" class="btn btn-sm btn-success rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width:32px; height:32px;"><i class="fas fa-phone-alt"></i></a>
                                    <button type="button" onclick="openEditContactModal({{ $contact->id }}, `{{ htmlspecialchars($contact->name, ENT_QUOTES) }}`, `{{ htmlspecialchars($contact->job_title ?? '', ENT_QUOTES) }}`, `{{ htmlspecialchars($contact->phone ?? '', ENT_QUOTES) }}`)" class="btn btn-sm btn-outline-primary rounded-circle shadow-sm hover-zoom d-flex align-items-center justify-content-center" style="width:32px; height:32px;" title="تعديل"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST" class="m-0 p-0">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle shadow-sm hover-zoom d-flex align-items-center justify-content-center" style="width:32px; height:32px;" onclick="return confirmAction(event, this.closest('form'), 'سيتم حذف جهة الاتصال؟')" title="حذف"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small text-center my-3">لا توجد جهات اتصال.</p>
                        @endforelse
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tasks" role="tabpanel">
        <div class="custom-card border-top border-success border-4 h-100">
            <div class="custom-card-header d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-tasks text-success me-2"></i> مهام العميل الحالية</h5>
                <div class="d-flex gap-2">
                    <select id="taskSortSelect" class="form-select form-select-sm shadow-sm" onchange="sortTasks(this.value)" style="width: 150px;">
                        <option value="default">الترتيب الافتراضي</option>
                        <option value="priority">الأهمية (عاجلة أولاً)</option>
                        <option value="date_asc">التاريخ (الأقرب أولاً)</option>
                    </select>
                    @if($isAdminAuth)
                    <button data-bs-toggle="modal" data-bs-target="#addTaskModal" class="btn btn-sm btn-success fw-bold rounded-pill px-3 shadow-sm">
                        <i class="fas fa-plus me-1"></i> تكليف
                    </button>
                    @endif
                </div>
            </div>
            @php
                $displayTasks = $client->tasks->where('status', '!=', 'completed');
                if(!$isAdminAuth) {
                    $displayTasks = $displayTasks->filter(function($task) {
                        return $task->assignedUsers->pluck('id')->contains(auth()->id());
                    });
                }
            @endphp
            <div class="card-body p-0 overflow-auto">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0" id="tasksTable">
                        <thead class="bg-light text-muted small border-bottom sticky-top">
                            <tr>
                                <th class="px-4 py-3">المهمة</th>
                                <th>الأولوية</th>
                                <th>تاريخ التسليم</th>
                                <th>الموظف</th>
                                <th class="text-center">إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($displayTasks as $index => $task)
                                <tr class="border-bottom task-row" data-original-order="{{ $index }}" data-priority="{{ $task->priority == 'high' ? 3 : ($task->priority == 'medium' ? 2 : 1) }}" data-date="{{ $task->deadline }}">
                                    <td class="px-4 py-3 fw-bold text-dark" style="max-width: 350px;">
                                        <div class="text-truncate">{{ $task->task_desc }}</div>
                                        @if($task->attachment_url)
                                            <a href="{{ $task->attachment_url }}" target="_blank" class="badge bg-light text-primary border text-decoration-none mt-2 d-inline-block hover-zoom">
                                                <i class="fas fa-paperclip me-1"></i> مرفق خارجي
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->priority == 'high') <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-3">عالية</span>
                                        @elseif($task->priority == 'medium') <span class="badge bg-warning bg-opacity-10 text-dark border border-warning rounded-pill px-3">متوسطة</span>
                                        @else <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-3">عادية</span> @endif
                                    </td>
                                    <td dir="ltr" class="text-end text-muted small fw-bold"><i class="far fa-calendar me-1"></i> {{ \Carbon\Carbon::parse($task->deadline)->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($task->assignedUsers as $u) <span class="badge bg-light text-dark border">{{ $u->username }}</span> @endforeach
                                        </div>
                                    <td class="text-center d-flex justify-content-center gap-1 border-0">
                                    @if($isAdminAuth)
                                        <button type="button" onclick="openEditTaskModal(this)" data-id="{{ $task->id }}" data-desc="{{ htmlspecialchars($task->task_desc, ENT_QUOTES) }}" data-deadline="{{ $task->deadline }}" data-priority="{{ $task->priority }}" data-client="{{ $task->client_id }}" data-recurrence="{{ $task->recurrence_type }}" data-recurrence-end="{{ $task->recurrence_end_date }}" data-attachment="{{ $task->attachment_url }}" data-users="{{ json_encode($task->assignedUsers->pluck('id')) }}" class="btn btn-sm btn-outline-primary rounded-circle shadow-sm hover-zoom" style="width:36px; height:36px;" title="تعديل المهمة">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="m-0 p-0">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle shadow-sm hover-zoom" style="width:36px; height:36px;" title="حذف المهمة" onclick="return confirmAction(event, this.closest('form'), 'هل أنت متأكد من حذف هذه المهمة نهائياً؟', 'error', '#d33')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <button type="button" onclick="openCompleteModal({{ $task->id }})" class="btn btn-sm btn-success rounded-circle shadow-sm hover-zoom" style="width:36px; height:36px;" title="إنجاز المهمة">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <div class="icon-box-sm bg-success bg-opacity-10 text-success mb-3" style="width: 60px; height: 60px; font-size: 24px;"><i class="fas fa-clipboard-check"></i></div>
                                        <h6 class="fw-bold text-dark">ممتاز! لا توجد مهام معلقة.</h6>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($isAdminAuth)
    <div class="tab-pane fade" id="documents" role="tabpanel">
        <div class="custom-card border-top border-danger border-4 p-4 h-100 overflow-auto">
            <div class="d-flex justify-content-between align-items-center mb-4 sticky-top bg-white py-2">
                <h5 class="fw-bold text-dark mb-0"><i class="fab fa-google-drive text-danger me-2"></i> وثائق العميل (Drive)</h5>
                <button class="btn btn-danger rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#addDocModal"><i class="fas fa-link me- link me-1"></i> ربط وثيقة جديدة</button>
            </div>
            <div class="row g-3">
                @forelse($client->documents as $doc)
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between align-items-center bg-light border border-danger border-opacity-10 p-3 rounded-4 hover-zoom h-100 gap-2">
                            <span class="small fw-bold text-dark text-truncate" title="{{ $doc->name }}"><i class="fas fa-file-pdf text-danger me-2 fs-4 align-middle"></i> {{ $doc->name }}</span>
                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                <a href="{{ $doc->drive_url }}" target="_blank" class="btn btn-sm btn-light text-danger rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width:32px; height:32px;" title="فتح الرابط"><i class="fas fa-external-link-alt"></i></a>
                                <button type="button" onclick="openEditDocModal({{ $doc->id }}, `{{ htmlspecialchars($doc->name, ENT_QUOTES) }}`, `{{ htmlspecialchars($doc->drive_url, ENT_QUOTES) }}`)" class="btn btn-sm btn-outline-primary rounded-circle shadow-sm hover-zoom d-flex align-items-center justify-content-center" style="width:32px; height:32px;" title="تعديل"><i class="fas fa-edit"></i></button>
                                <form action="{{ route('attachments.destroy', $doc->id) }}" method="POST" class="m-0 p-0">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle shadow-sm hover-zoom d-flex align-items-center justify-content-center" style="width:32px; height:32px;" onclick="return confirmAction(event, this.closest('form'), 'سيتم حذف رابط الوثيقة نهائياً؟')" title="حذف"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-5">لا توجد وثائق مربوطة.</div>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    <div class="tab-pane fade" id="chat" role="tabpanel">
        <div class="chat-card">
            
            <div class="chat-header bg-white border-bottom pt-3 pb-3 px-4 shadow-sm" style="z-index:10;">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-comments text-primary me-2 fs-4 align-middle"></i> 
                        {{ $isWorkspace ? 'شات فريق العمل' : 'سجل المتابعة والتفاعل' }}
                    </h5>
                    
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="input-group input-group-sm bg-light rounded-pill p-1 border shadow-sm" style="max-width:250px;">
                            <span class="input-group-text bg-transparent border-0 text-muted px-3"><i class="fas fa-search"></i></span>
                            <input type="text" id="chatSearchInput" onkeyup="searchChat()" class="form-control border-0 bg-transparent px-1" style="box-shadow: none;" placeholder="ابحث في السجل...">
                        </div>

                        <div class="btn-group btn-group-sm shadow-sm" role="group" dir="ltr">
                            <button type="button" class="btn btn-outline-warning fw-bold px-3 text-dark" onclick="filterChat('pinned')"><i class="fas fa-thumbtack me-1"></i> هام</button>
                            <button type="button" class="btn btn-outline-primary fw-bold px-3 active" onclick="filterChat('all')" id="btn-all-chat">الكل</button>
                        </div>
                        
                        @if($isAdminAuth)
                            <form action="{{ route('operations.clear', $client->id) }}" method="POST" class="m-0 p-0">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger fw-bold shadow-sm rounded-pill px-3" onclick="return confirmAction(event, this.closest('form'), 'سيتم مسح سجل الشات بالكامل لهذا العميل، ولا يمكن التراجع! هل أنت متأكد؟', 'error', '#d33')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            @php $pinnedMessages = $operations->where('is_pinned', true); @endphp
            @if($pinnedMessages->count() > 0)
            <div class="px-4 py-2 bg-white border-bottom shadow-sm d-flex align-items-center overflow-auto" style="white-space: nowrap; gap: 8px;">
                <i class="fas fa-thumbtack text-warning fs-5"></i> 
                <strong class="small text-dark me-2">رسائل مثبتة:</strong>
                @foreach($pinnedMessages as $pm)
                    <button type="button" onclick="scrollToMsg({{ $pm->id }})" class="btn btn-sm btn-light rounded-pill border border-warning border-opacity-50 shadow-sm hover-zoom px-3" style="max-width: 220px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; font-size: 0.8rem;">
                        {{ \Illuminate\Support\Str::limit(strip_tags($pm->action_text), 35) }}
                    </button>
                @endforeach
            </div>
            @endif

            <div class="chat-container flex-grow-1 p-4" id="chatBox">
                @php $lastDate = null; @endphp
                
                @forelse($operations as $op)
                    @php
                        $isMe = $op->user_id === auth()->id();
                        $currentDate = $op->created_at->format('Y-m-d');
                    @endphp

                    @if($currentDate !== $lastDate)
                        <div class="chat-date-separator">
                            @if($currentDate == now()->format('Y-m-d')) اليوم
                            @elseif($currentDate == now()->subDay()->format('Y-m-d')) أمس
                            @else {{ \Carbon\Carbon::parse($currentDate)->format('d M Y') }} @endif
                        </div>
                        @php $lastDate = $currentDate; @endphp
                    @endif

                    @if($op->is_system)
    <div id="msg-{{ $op->id }}" class="d-flex justify-content-center w-100 my-2 chat-searchable" data-msg-id="{{ $op->id }}">
        <span class="system-msg-line shadow-sm"><i class="fas fa-info-circle me-1 text-secondary"></i> {!! $op->formatted_text ?? strip_tags($op->action_text, '<b><i><u><strong><em><br><a><span>') !!} <span class="ms-2 opacity-50" dir="ltr">{{ $op->created_at->format('h:i A') }}</span></span>
    </div>
@else
    <div id="msg-{{ $op->id }}" class="chat-message chat-searchable d-flex align-items-start gap-3 w-100 mb-3 {{ $isMe ? 'ms-auto flex-row-reverse' : '' }} {{ $op->is_pinned ? 'msg-pinned' : '' }}" data-msg-id="{{ $op->id }}">
        
        <div class="avatar shadow-sm mt-1 {{ $isMe ? 'bg-avatar-mine' : 'bg-avatar-other' }}">
            {{ mb_substr($op->user->username ?? 'ن', 0, 2) }}
        </div>

                            <div class="chat-bubble shadow-sm {{ $isMe ? 'bg-msg-mine' : 'bg-msg-other' }}">
                                @if($op->is_pinned)
                                    <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-warning text-dark shadow-sm border border-white" style="margin-top: -5px; z-index: 10;"><i class="fas fa-thumbtack"></i></span>
                                @endif

                                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom {{ $isMe ? 'border-light border-opacity-25' : 'border-secondary border-opacity-10' }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold {{ $isMe ? 'text-white' : 'text-primary' }}" style="font-size: 0.85rem;">{{ $op->user->username ?? 'نظام' }}</span>
                                        <span class="{{ $isMe ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.65rem;" dir="ltr">{{ $op->created_at->format('h:i A') }}</span>
                                    </div>
                                    
                                    <div class="dropdown ms-2">
                                        <button class="btn btn-sm p-0 shadow-none border-0 text-{{ $isMe ? 'white' : 'muted' }} hover-zoom d-flex align-items-center justify-content-center" style="opacity: 0.8;" type="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v px-2"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3" style="min-width: 120px; font-size: 0.9rem;">
                                            <li>
                                                <button type="button" class="dropdown-item text-primary fw-bold" onclick="prepareReply({{ $op->id }}, `{{ htmlspecialchars(\Illuminate\Support\Str::limit(strip_tags($op->action_text), 40), ENT_QUOTES) }}`)">
                                                    <i class="fas fa-reply me-2"></i> رد
                                                </button>
                                            </li>
                                            @php
                                                $canEdit = $isAdminAuth || $isMe; 
                                                $canPin = $isAdminAuth;
                                            @endphp
                                            @if($canEdit)
                                            <li>
                                                <button type="button" class="dropdown-item text-success fw-bold" onclick="openEditMessageModal({{ $op->id }}, `{{ htmlspecialchars($op->action_text, ENT_QUOTES) }}`)"><i class="fas fa-edit me-2"></i> تعديل</button>
                                            </li>
                                            @endif
                                            @if($isAdminAuth)
                                            <li>
                                                <form action="{{ route('operations.destroy', $op->id) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger fw-bold" onclick="return confirmAction(event, this.closest('form'), 'سيتم مسح هذه الرسالة نهائياً؟')"><i class="fas fa-trash-alt me-2"></i> حذف للأدمن</button>
                                                </form>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                
                                @if($op->replyTo)
                                    <div onclick="scrollToMsg({{ $op->replyTo->id }})" class="p-2 mb-2 rounded-2 hover-zoom" style="background: {{ $isMe ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.03)' }}; font-size: 0.8rem; border-right: 3px solid {{ $isMe ? 'var(--gold-color)' : 'var(--primary-color)' }} !important; cursor: pointer;">
                                        <strong class="{{ $isMe ? 'text-warning' : 'text-primary' }} d-block mb-1"><i class="fas fa-reply me-1"></i> {{ $op->replyTo->user->username ?? '---' }}</strong>
                                        <span class="text-truncate d-block text-{{ $isMe ? 'white-50' : 'muted' }}" style="max-width: 100%; opacity: 0.9;">{{ \Illuminate\Support\Str::limit(strip_tags($op->replyTo->action_text), 60) }}</span>
                                    </div>
                                @endif

                                <div class="chat-content text-break" style="line-height: 1.6; font-size: 0.95rem;">
    {!! $op->formatted_text ?? nl2br(e($op->action_text)) !!}
</div>

                                @if($op->attachment_url)
                                    <div class="mt-2">
                                        <a href="{{ $op->attachment_url }}" target="_blank" class="btn btn-sm shadow-sm w-100 fw-bold hover-zoom {{ $isMe ? 'bg-white text-primary border-0' : 'bg-light border text-dark' }} rounded-pill">
                                            <i class="fas fa-link me-1"></i> فتح الرابط المرفق
                                        </a>
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between align-items-center mt-2 pt-1 border-top {{ $isMe ? 'border-light border-opacity-10' : 'border-secondary border-opacity-10' }}">
                                    <div class="reactions-container d-flex flex-wrap gap-1 align-items-center">
                                        @php 
                                            $groupedReactions = ($op->reactions ?? collect())->groupBy('emoji'); 
                                        @endphp
                                        @foreach($groupedReactions as $emoji => $reactions)
                                            @php 
                                                $iReacted = $reactions->contains('user_id', auth()->id()); 
                                                $reactorsNames = $reactions->map(function($r) { return $r->user->username ?? 'نظام'; })->implode('، ');
                                            @endphp
                                            <form action="{{ route('operations.react', $op->id) }}" method="POST" class="m-0 p-0">
                                                @csrf <input type="hidden" name="emoji" value="{{ $emoji }}">
                                                <button type="submit" class="badge rounded-pill border shadow-sm px-2 py-1 hover-zoom {{ $iReacted ? 'bg-primary text-white border-primary' : 'bg-white text-dark border-light' }}" style="cursor:pointer; font-size:0.75rem;" title="{{ $reactorsNames }}">
                                                    {{ $emoji }} {{ $reactions->count() }}
                                                </button>
                                            </form>
                                        @endforeach

                                        <div class="dropup reactions-dropup">
                                            <button class="badge rounded-pill border text-muted shadow-sm px-2 py-1 hover-zoom {{ $isMe ? 'bg-white bg-opacity-10 text-white border-light' : 'bg-white border-light' }}" style="cursor:pointer;" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="far fa-smile"></i></button>
                                            <ul class="dropdown-menu dropdown-menu-start p-2 shadow-lg border-0 rounded-4" style="min-width: auto; background: rgba(255,255,255,0.98); backdrop-filter: blur(10px);">
                                                <li>
                                                    <div class="d-flex flex-row gap-1 px-1">
                                                        @foreach(['👍', '❤️', '✅', '😂', '👀'] as $e)
                                                            <form action="{{ route('operations.react', $op->id) }}" method="POST" class="m-0 p-0">
                                                                @csrf <input type="hidden" name="emoji" value="{{ $e }}">
                                                                <button type="submit" class="btn btn-sm btn-light rounded-circle p-1 fs-5 d-flex align-items-center justify-content-center hover-zoom" style="width:38px; height:38px;">{{ $e }}</button>
                                                            </form>
                                                        @endforeach
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        @if($op->is_edited) <small class="{{ $isMe ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.65rem;" title="تم التعديل"><i class="fas fa-pen"></i></small> @endif
                                        @if($canPin)
                                            <form action="{{ route('operations.pin', $op->id) }}" method="POST" class="m-0 p-0">
                                                @csrf
                                                <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent {{ $isMe ? 'text-white-50' : 'text-muted' }} hover-zoom" title="{{ $op->is_pinned ? 'إلغاء التثبيت' : 'تثبيت كمرجع' }}"><i class="fas fa-thumbtack {{ $op->is_pinned ? 'text-warning fs-6' : '' }}"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="text-center text-muted my-auto opacity-50 p-5">
                        <i class="fas fa-comments fa-4x mb-3 text-secondary"></i>
                        <h6 class="fw-bold">لا توجد رسائل أو تحديثات بعد.</h6>
                    </div>
                @endforelse
            </div>

            <div class="chat-input-area p-3 px-4 sticky-bottom border-top">
                <div id="replyPreviewBox" class="bg-light p-2 border border-bottom-0 rounded-top-4 d-none justify-content-between align-items-center mx-3 reply-box-animation">
                    <div class="small text-muted text-truncate"><i class="fas fa-reply text-primary me-2"></i> <span id="replyPreviewText" class="fw-bold text-dark"></span></div>
                    <button type="button" class="btn-close btn-sm shadow-none" onclick="cancelReply()"></button>
                </div>
                
                <form action="{{ route('operations.store', $client->id) }}" method="POST" id="chatSendMessageForm">
                    @csrf
                    <input type="hidden" name="reply_to_id" id="replyToIdInput">
                    
                    <div class="chat-input-box shadow-sm gap-2 d-flex align-items-end p-2 px-3">
                        <button class="btn btn-sm btn-light rounded-circle text-muted hover-zoom d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;" type="button" onclick="toggleLinkInput()" title="إرفاق رابط خارجي">
                            <i class="fas fa-paperclip fs-5"></i>
                        </button>
                        
                        <textarea name="action_text" id="chatTextareaInput" class="form-control chat-textarea" rows="1" placeholder="اكتب ملاحظة، رد، أو تحديث... (Enter للإرسال، Shift+Enter لسطر جديد)" required autocomplete="off"></textarea>
                        
                        <button class="btn btn-primary rounded-circle shadow hover-zoom d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background: var(--primary-color); border: none;" type="submit" title="إرسال (Enter)">
                            <i class="fas fa-paper-plane text-white fs-6"></i>
                        </button>
                    </div>

                    <div id="linkInputBox" class="mt-2 d-none reply-box-animation px-3 pb-2">
                        <div class="input-group input-group-sm rounded-pill shadow-sm border p-1 bg-white">
                            <span class="input-group-text bg-transparent border-0 text-primary px-3"><i class="fas fa-link"></i></span>
                            <input type="url" name="attachment_url" class="form-control border-0 bg-transparent px-1 text-start" dir="ltr" placeholder="الصق الرابط هنا (https://...)">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($isAdminAuth)

<div class="modal fade" id="editDocModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editDocForm" method="POST" class="modal-content rounded-4 border-0 shadow-lg">
            @csrf @method('PUT')
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> تعديل بيانات الوثيقة</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">اسم الوثيقة</label>
                    <input type="text" name="name" id="edit_doc_name" class="form-control rounded-pill px-3 shadow-sm border-light" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">رابط المشاركة (Drive URL)</label>
                    <input type="url" name="drive_url" id="edit_doc_url" class="form-control rounded-pill px-3 shadow-sm border-light text-start" dir="ltr" required>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light pt-0">
                <button type="submit" class="btn btn-danger rounded-pill text-white fw-bold w-100 shadow-sm">حفظ التعديلات</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editClientModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-light border-0 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit text-primary me-2"></i> تعديل بيانات: {{ $client->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('clients.update', $client->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">اسم العميل</label>
                            <input type="text" name="name" value="{{ $client->name }}" class="form-control shadow-sm border-light" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">حالة العميل</label>
                            <select name="status" class="form-select shadow-sm border-light" required>
                                <option value="active" {{ $client->status == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ $client->status == 'inactive' ? 'selected' : '' }}>معطل</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">نوع الباقة</label>
                            <select name="package_type" class="form-select shadow-sm border-light" required>
                                <option value="basic" {{ $client->package_type == 'basic' ? 'selected' : '' }}>أساسية</option>
                                <option value="advanced" {{ $client->package_type == 'advanced' ? 'selected' : '' }}>متقدمة</option>
                                <option value="professional" {{ $client->package_type == 'professional' ? 'selected' : '' }}>احترافية</option>
                                <option value="comprehensive" {{ $client->package_type == 'comprehensive' ? 'selected' : '' }}>شاملة</option>
                                <option value="custom" class="fw-bold text-primary" {{ $client->package_type == 'custom' ? 'selected' : '' }}>مخصصة</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-success">قيمة الاشتراك (بالريال)</label>
                            <input type="number" step="0.01" name="subscription_amount" value="{{ $client->subscription_amount }}" class="form-control shadow-sm border-success" placeholder="مثال: 1500.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">الرقم الضريبي</label>
                            <input type="text" name="tax_number" value="{{ $client->tax_number }}" class="form-control shadow-sm border-light" placeholder="اختياري">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">السجل التجاري</label>
                            <input type="text" name="commercial_register" value="{{ $client->commercial_register }}" class="form-control shadow-sm border-light" placeholder="اختياري">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">بداية الاشتراك</label>
                            <input type="date" name="sub_start_date" id="edit_start_date" value="{{ !empty($client->sub_start_date) ? \Carbon\Carbon::parse($client->sub_start_date)->format('Y-m-d') : '' }}" class="form-control shadow-sm border-light">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">مدة الاشتراك</label>
                            <select name="subscription_duration" id="edit_duration" class="form-select shadow-sm border-light">
                                <option value="" {{ empty($client->subscription_duration) ? 'selected' : '' }}>غير محدد</option>
                                <option value="monthly" {{ $client->subscription_duration == 'monthly' ? 'selected' : '' }}>شهري</option>
                                <option value="quarterly" {{ $client->subscription_duration == 'quarterly' ? 'selected' : '' }}>ربع سنوي</option>
                                <option value="semi_annual" {{ $client->subscription_duration == 'semi_annual' ? 'selected' : '' }}>نصف سنوي</option>
                                <option value="annual" {{ $client->subscription_duration == 'annual' ? 'selected' : '' }}>سنوي</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-danger">تاريخ الانتهاء</label>
                            <input type="date" id="edit_end_date" name="sub_end_date" value="{{ !empty($client->sub_end_date) ? \Carbon\Carbon::parse($client->sub_end_date)->format('Y-m-d') : '' }}" class="form-control shadow-sm border-light bg-light" readonly>
                        </div>
                        <div class="col-md-12 border-top pt-3 mt-3">
                            <label class="form-label fw-bold text-primary mb-3"><i class="fas fa-eye me-1"></i> نطاق رؤية العميل</label>
                            <div class="d-flex flex-wrap gap-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input visibility-radio" type="radio" name="visibility" id="vis_all_show" value="all" {{ $client->visibility == 'all' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-success" for="vis_all_show">متاح للجميع</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input visibility-radio" type="radio" name="visibility" id="vis_specific_show" value="specific" {{ $client->visibility == 'specific' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-primary" for="vis_specific_show">تخصيص لموظفين محددين</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input visibility-radio" type="radio" name="visibility" id="vis_admins_show" value="admins_only" {{ $client->visibility == 'admins_only' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-danger" for="vis_admins_show">مغلق للإدارة فقط</label>
                                </div>
                            </div>
                            <div class="border rounded-4 p-3 bg-white shadow-sm employees-box" style="max-height: 150px; overflow-y: auto; {{ $client->visibility == 'specific' ? 'display: block;' : 'display: none;' }}">
                                @php $assignedUserIds = $client->assignedUsers->pluck('id')->toArray(); @endphp
                                @foreach(\App\Models\User::where('role', '!=', 'admin')->get() as $u)
                                    <div class="form-check mb-2 border-bottom pb-1">
                                        <input class="form-check-input" type="checkbox" name="assigned_users[]" value="{{ $u->id }}" id="edit_u_{{ $u->id }}_show" {{ in_array($u->id, $assignedUserIds) ? 'checked' : '' }}>
                                        <label class="form-check-label small text-dark fw-bold" for="edit_u_{{ $u->id }}_show">{{ $u->username }}</label>
                                    </div>
                                @endforeach
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

<div class="modal fade" id="addDocModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('attachments.store', $client->id) }}" method="POST" class="modal-content rounded-4 border-0 shadow-lg">
            @csrf
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="fab fa-google-drive me-2"></i> ربط وثيقة من جوجل درايف</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">اسم الوثيقة</label>
                    <input type="text" name="name" class="form-control rounded-pill px-3 shadow-sm border-light" placeholder="مثال: السجل التجاري 2026" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">رابط المشاركة (Drive URL)</label>
                    <input type="url" name="drive_url" class="form-control rounded-pill px-3 shadow-sm border-light text-start" dir="ltr" placeholder="https://drive.google.com/..." required>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light pt-0">
                <button type="submit" class="btn btn-danger rounded-pill fw-bold w-100 shadow-sm">حفظ الرابط</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addContactModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('contacts.store', $client->id) }}" method="POST" class="modal-content rounded-4 border-0 shadow-lg">
                @csrf
            <div class="modal-header bg-warning text-dark border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-address-book me-2"></i> إضافة جهة اتصال</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">الاسم</label>
                    <input type="text" name="name" class="form-control rounded-pill px-3 shadow-sm border-light" placeholder="مثال: أ. أحمد" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold small text-muted">الصفة / الوظيفة</label>
                        <input type="text" name="job_title" class="form-control rounded-pill px-3 shadow-sm border-light" placeholder="المدير المالي">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small text-muted">رقم الهاتف</label>
                        <input type="text" name="phone" class="form-control rounded-pill px-3 shadow-sm border-light text-start" dir="ltr" placeholder="+201XXXXXXXXX">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light pt-0">
                <button type="submit" class="btn btn-warning rounded-pill text-dark fw-bold w-100 shadow-sm">حفظ جهة الاتصال</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('notes.store', $client->id) }}" method="POST" class="modal-content rounded-4 border-0 shadow-lg">
                @csrf
            <div class="modal-header bg-info text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-sticky-note me-2"></i> إضافة ملاحظة أو تنبيه</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light text-start">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">نوع العنصر</label>
                    <select name="type" class="form-select rounded-pill px-3 shadow-sm border-light">
                        <option value="note">ملاحظة إدارية سرية (تراها الإدارة فقط)</option>
                        <option value="alert">تنبيه هام جداً (يراه الموظف والإدارة)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">المحتوى</label>
                    <textarea name="content" class="form-control rounded-4 p-3 shadow-sm border-light" rows="4" placeholder="اكتب هنا..." required></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light pt-0">
                <button type="submit" class="btn btn-info rounded-pill text-white fw-bold w-100 shadow-sm">إضافة وحفظ</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addTaskModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-light border-0 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-thumbtack text-warning me-2"></i> إنشاء تكليف جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tasks.store') }}" method="POST" id="addTaskFormId">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">العميل المستهدف</label>
                            <select name="client_id" id="dynamic_client_select" class="form-select shadow-sm border-light" required>
                                <option value="" disabled selected>-- اختر العميل --</option>
                                @foreach(\App\Models\Client::where('status', '!=', 'completed')->get() as $c)
                                    <option value="{{ $c->id }}" {{ (isset($client) && $client->id == $c->id) ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
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
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">العميل المستهدف</label>
                            <select name="client_id" id="edit_task_client_id" class="form-select shadow-sm border-light" required>
                                <option value="" disabled>-- اختر العميل --</option>
                                @foreach(\App\Models\Client::where('status', '!=', 'completed')->get() as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
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

<div class="modal fade" id="editNoteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editNoteForm" method="POST" class="modal-content rounded-4 border-0 shadow-lg">
            @csrf @method('PUT')
            <div class="modal-header bg-info text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> تعديل الملاحظة/التنبيه</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light text-start">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">نوع العنصر</label>
                    <select name="type" id="edit_note_type" class="form-select rounded-pill px-3 shadow-sm border-light">
                        <option value="note">ملاحظة إدارية سرية (تراها الإدارة فقط)</option>
                        <option value="alert">تنبيه هام جداً (يراه الموظف والإدارة)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">المحتوى</label>
                    <textarea name="content" id="edit_note_content" class="form-control rounded-4 p-3 shadow-sm border-light" rows="4" required></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light pt-0">
                <button type="submit" class="btn btn-info rounded-pill text-white fw-bold w-100 shadow-sm">حفظ التعديلات</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editContactModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editContactForm" method="POST" class="modal-content rounded-4 border-0 shadow-lg">
            @csrf @method('PUT')
            <div class="modal-header bg-warning text-dark border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> تعديل جهة الاتصال</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">الاسم</label>
                    <input type="text" name="name" id="edit_contact_name" class="form-control rounded-pill px-3 shadow-sm border-light" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold small text-muted">الصفة / الوظيفة</label>
                        <input type="text" name="job_title" id="edit_contact_job" class="form-control rounded-pill px-3 shadow-sm border-light">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small text-muted">رقم الهاتف</label>
                        <input type="text" name="phone" id="edit_contact_phone" class="form-control rounded-pill px-3 shadow-sm border-light text-start" dir="ltr">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light pt-0">
                <button type="submit" class="btn btn-warning rounded-pill text-dark fw-bold w-100 shadow-sm">حفظ التعديلات</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editMessageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editMessageForm" method="POST" class="modal-content rounded-4 border-0 shadow-lg">
            @csrf @method('PUT')
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-pen me-2"></i> تعديل الرسالة</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="mb-3">
                    <textarea name="action_text" id="editMessageText" class="form-control rounded-4 p-3 shadow-sm border-light" rows="4" required></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light pt-0">
                <button type="submit" class="btn btn-primary rounded-pill fw-bold w-100 shadow-sm">حفظ التعديل</button>
            </div>
        </form>
    </div>
</div>
@endif

@auth
<div class="modal fade" id="completeTaskModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="completeTaskForm" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
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
@endauth

@endsection

@section('scripts')
<script>
    // 🟢 Textarea Auto-grow 🟢
    const tx = document.getElementById('chatTextareaInput');
    if(tx) {
        tx.setAttribute('style', 'height:' + (tx.scrollHeight) + 'px;overflow-y:hidden;');
        tx.addEventListener("input", OnInput, false);
        
        tx.addEventListener("keydown", function(e) {
            if (e.key === "Enter" && !e.shiftKey) {
                e.preventDefault();
                if(this.value.trim() !== "") {
                    document.getElementById('chatSendMessageForm').submit();
                }
            }
        });
    }

    function OnInput() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
        if(this.value.trim() === '') {
            this.style.height = '42px';
        }
    }

    // 🟢 فرز المهام (Sorting) معدل ليعود للترتيب الأصلي 🟢
    function sortTasks(criteria) {
        let tbody = document.querySelector('#tasksTable tbody');
        let rows = Array.from(tbody.querySelectorAll('tr.task-row'));

        rows.sort((a, b) => {
            if (criteria === 'default') {
                return parseInt(a.getAttribute('data-original-order')) - parseInt(b.getAttribute('data-original-order'));
            } else if (criteria === 'date_asc') {
                return new Date(a.getAttribute('data-date')) - new Date(b.getAttribute('data-date'));
            } else if (criteria === 'priority') {
                return parseInt(b.getAttribute('data-priority')) - parseInt(a.getAttribute('data-priority'));
            }
        });

        rows.forEach(row => tbody.appendChild(row));
    }

    function openEditDocModal(id, name, url) {
        document.getElementById('editDocForm').action = '/attachments/' + id;
        document.getElementById('edit_doc_name').value = name;
        document.getElementById('edit_doc_url').value = url;
        new bootstrap.Modal(document.getElementById('editDocModal')).show();
    }
    function openEditNoteModal(id, type, content) {
        document.getElementById('editNoteForm').action = '/notes/' + id;
        document.getElementById('edit_note_type').value = type;
        document.getElementById('edit_note_content').value = content;
        new bootstrap.Modal(document.getElementById('editNoteModal')).show();
    }

    function openEditContactModal(id, name, jobTitle, phone) {
        document.getElementById('editContactForm').action = '/contacts/' + id;
        document.getElementById('edit_contact_name').value = name;
        document.getElementById('edit_contact_job').value = jobTitle;
        document.getElementById('edit_contact_phone').value = phone;
        new bootstrap.Modal(document.getElementById('editContactModal')).show();
    }

    function prepareReply(id, text) {
        document.getElementById('replyToIdInput').value = id;
        document.getElementById('replyPreviewText').innerText = text;
        document.getElementById('replyPreviewBox').classList.remove('d-none');
        document.getElementById('replyPreviewBox').classList.add('d-flex');
        document.getElementById('chatTextareaInput').focus(); 
    }
    function cancelReply() {
        document.getElementById('replyToIdInput').value = '';
        document.getElementById('replyPreviewBox').classList.remove('d-flex');
        document.getElementById('replyPreviewBox').classList.add('d-none');
    }
    function scrollToMsg(id) {
        let msg = document.getElementById('msg-' + id);
        let chatBox = document.getElementById('chatBox');
        if(msg && chatBox) {
            chatBox.scrollTo({ top: msg.offsetTop - 80, behavior: 'smooth' });
            msg.classList.add('highlight-msg');
            setTimeout(() => msg.classList.remove('highlight-msg'), 2500);
        }
    }

    function toggleLinkInput() {
        let box = document.getElementById('linkInputBox');
        if(box.classList.contains('d-none')) {
            box.classList.remove('d-none');
            box.querySelector('input').focus();
        } else {
            box.classList.add('d-none');
            box.querySelector('input').value = ''; 
        }
    }

    function openEditMessageModal(operationId, text) {
        document.getElementById('editMessageText').value = text;
        document.getElementById('editMessageForm').action = '/operations/' + operationId;
        new bootstrap.Modal(document.getElementById('editMessageModal')).show();
    }

    function searchChat() {
        let input = document.getElementById('chatSearchInput').value.toLowerCase();
        document.querySelectorAll('.chat-searchable').forEach(msg => {
            let text = msg.innerText.toLowerCase();
            if(text.includes(input)) { msg.classList.remove('d-none'); msg.classList.add('d-flex'); } 
            else { msg.classList.remove('d-flex'); msg.classList.add('d-none'); }
        });
    }

    function filterChat(type) {
        document.querySelectorAll('.btn-group .btn').forEach(btn => {
            btn.classList.remove('active', 'btn-primary', 'btn-warning');
            btn.classList.add('btn-outline-primary');
            if(btn.innerHTML.includes('هام')) btn.classList.add('btn-outline-warning', 'text-dark');
        });
        
        let clickedBtn = event.currentTarget;
        clickedBtn.classList.remove('btn-outline-primary', 'btn-outline-warning', 'text-dark');
        clickedBtn.classList.add(type === 'pinned' ? 'btn-warning' : 'btn-primary', 'active');
        if(type === 'pinned') clickedBtn.classList.add('text-dark');

        document.querySelectorAll('.chat-message').forEach(msg => {
            let isSystem = msg.querySelector('.system-msg-line');
            if(isSystem && type === 'all') { msg.classList.remove('d-none'); msg.classList.add('d-flex'); return; }
            if(isSystem && type !== 'all') { msg.classList.remove('d-flex'); msg.classList.add('d-none'); return; }

            let isPinned = msg.classList.contains('msg-pinned');
            let shouldShow = (type === 'all') || (type === 'pinned' && isPinned);
            if (shouldShow) { msg.classList.remove('d-none'); msg.classList.add('d-flex'); } 
            else { msg.classList.remove('d-flex'); msg.classList.add('d-none'); }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        let activeTab = localStorage.getItem('activeClientTab');
        if(activeTab){
            let tabElement = document.querySelector(`button[data-bs-target="${activeTab}"]`);
            if(tabElement) new bootstrap.Tab(tabElement).show();
        }
        document.querySelectorAll('button[data-bs-toggle="pill"]').forEach(t => {
            t.addEventListener('shown.bs.tab', function (e) {
                localStorage.setItem('activeClientTab', e.target.getAttribute('data-bs-target'));
                if(e.target.getAttribute('data-bs-target') === '#chat') {
                    var chatBox = document.getElementById("chatBox");
                    if(chatBox) { chatBox.scrollTop = chatBox.scrollHeight; }
                }
            });
        });

        var chatBox = document.getElementById("chatBox");
        if(chatBox) { chatBox.scrollTop = chatBox.scrollHeight; }

        // 🟢 تحسين أداء تحديث الشات (Smart DOM Diffing) 🟢
        setInterval(() => {
            if(document.getElementById('chat') && document.getElementById('chat').classList.contains('active')) {
                fetch(window.location.href)
                .then(res => res.text())
                .then(html => {
                    let parser = new DOMParser();
                    let doc = parser.parseFromString(html, 'text/html');
                    let newChatBox = doc.getElementById('chatBox');
                    let currentChatBox = document.getElementById('chatBox');
                    
                    // المقارنة على الـ HTML بالكامل لاكتشاف (التعديل، الحذف، الإضافة)
                    if(newChatBox && currentChatBox && newChatBox.innerHTML !== currentChatBox.innerHTML) {
                        let isScrolledToBottom = currentChatBox.scrollHeight - currentChatBox.clientHeight <= currentChatBox.scrollTop + 50;
                        currentChatBox.innerHTML = newChatBox.innerHTML;
                        if(isScrolledToBottom) {
                            currentChatBox.scrollTop = currentChatBox.scrollHeight;
                        }
                    }
                }).catch(err => console.error('Chat update paused'));
            }
        }, 5000); 

        document.querySelectorAll('.visibility-radio').forEach(radio => {
            radio.addEventListener('change', function () {
                let box = this.closest('.col-md-12').querySelector('.employees-box');
                if (this.value === 'specific') { box.style.display = 'block'; } 
                else { box.style.display = 'none'; box.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false); }
            });
        });

        // 🟢 دمج وتصحيح دالة جلب الموظفين لمنع التكرار وحل خطأ الـ selectedUsers 🟢
        function loadClientUsers(clientId, containerId, prefix, selectedUsers = []) {
            const container = document.getElementById(containerId);
            if(!clientId) return;
            container.innerHTML = '<div class="text-center mt-3"><div class="spinner-border spinner-border-sm text-primary"></div> جاري التحميل...</div>';
            fetch('/clients/' + clientId + '/users')
                .then(response => response.json())
                .then(users => {
                    container.innerHTML = '';
                    if(users.length === 0) { container.innerHTML = '<div class="text-danger small text-center mt-3">لا يوجد موظفين مخصصين.</div>'; return; }
                    users.forEach(u => {
                        let isAdmin = u.role === 'admin' ? '<span class="text-danger small float-end">(أدمن)</span>' : '';
                        let isChecked = selectedUsers.includes(u.id) ? 'checked' : '';
                        container.innerHTML += `
                            <div class="form-check mb-2 border-bottom pb-1">
                                <input class="form-check-input" type="checkbox" name="assigned_to[]" value="${u.id}" id="${prefix}_${u.id}" ${isChecked}>
                                <label class="form-check-label fw-bold small text-dark w-100" style="cursor: pointer;" for="${prefix}_${u.id}">${u.username} ${isAdmin}</label>
                            </div>`;
                    });
                }).catch(err => {
                    container.innerHTML = '<div class="text-danger small text-center mt-3">حدث خطأ أثناء تحميل الموظفين.</div>';
                });
        }

        const clientSelect = document.getElementById('dynamic_client_select');
        if(clientSelect) {
            clientSelect.addEventListener('change', function() { loadClientUsers(this.value, 'dynamic_users_container', 'dyn_user', []); });
            if(clientSelect.value) { loadClientUsers(clientSelect.value, 'dynamic_users_container', 'dyn_user', []); }
        }

        document.getElementById('edit_task_client_id')?.addEventListener('change', function() { 
            loadClientUsers(this.value, 'edit_dynamic_users_container', 'edit_dyn_user', []); 
        });

        // جعل الدالة متاحة للـ HTML مباشرة عبر window
        window.fetchUsersForEdit = function(clientId, selectedUsers) {
            loadClientUsers(clientId, 'edit_dynamic_users_container', 'edit_dyn_user', selectedUsers);
        };

        function calculateEndDateShow(startId, durationId, endId) {
            let startDate = document.getElementById(startId)?.value; let duration = document.getElementById(durationId)?.value; let endInput = document.getElementById(endId);
            if (startDate && duration && endInput) {
                let date = new Date(startDate);
                if (duration === 'monthly') date.setMonth(date.getMonth() + 1);
                else if (duration === 'quarterly') date.setMonth(date.getMonth() + 3);
                else if (duration === 'semi_annual') date.setMonth(date.getMonth() + 6);
                else if (duration === 'annual') date.setFullYear(date.getFullYear() + 1);
                endInput.value = date.toISOString().split('T')[0];
            } else if(endInput) endInput.value = '';
        }
        
        document.getElementById('edit_start_date')?.addEventListener('change', () => calculateEndDateShow('edit_start_date', 'edit_duration', 'edit_end_date'));
        document.getElementById('edit_duration')?.addEventListener('change', () => calculateEndDateShow('edit_start_date', 'edit_duration', 'edit_end_date'));

        document.getElementById('recurrence_select')?.addEventListener('change', function() { document.getElementById('recurrence_end_div').style.display = this.value === 'none' ? 'none' : 'block'; });
        document.getElementById('edit_recurrence_select')?.addEventListener('change', function() { document.getElementById('edit_recurrence_end_div').style.display = this.value === 'none' ? 'none' : 'block'; });

        document.getElementById('addTaskFormId')?.addEventListener('submit', function(e) {
            if(document.querySelectorAll('input[name="assigned_to[]"]:checked').length === 0) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'تنبيه هام!', text: 'اختر موظف واحد على الأقل!', confirmButtonColor: '#d33' });
            }
        });
    });

    function openCompleteModal(taskId) {
        document.getElementById('completeTaskForm').action = '/tasks/' + taskId + '/complete';
        new bootstrap.Modal(document.getElementById('completeTaskModal')).show();
    }

    function openEditTaskModal(btn) {
        let id = btn.getAttribute('data-id'); let desc = btn.getAttribute('data-desc'); let deadline = btn.getAttribute('data-deadline'); let priority = btn.getAttribute('data-priority'); let clientId = btn.getAttribute('data-client'); let recurrence = btn.getAttribute('data-recurrence'); let recurrenceEnd = btn.getAttribute('data-recurrence-end'); let users = JSON.parse(btn.getAttribute('data-users')); let attachment = btn.getAttribute('data-attachment') || ''; 
        document.getElementById('editTaskForm').action = '/tasks/' + id;
        document.getElementById('edit_task_desc').value = desc; document.getElementById('edit_task_attachment_url').value = attachment; 
        document.getElementById('edit_task_priority').value = priority; document.getElementById('edit_task_client_id').value = clientId; document.getElementById('edit_recurrence_select').value = recurrence || 'none';
        if (recurrence && recurrence !== 'none') { document.getElementById('edit_recurrence_end_div').style.display = 'block'; document.getElementById('edit_recurrence_end_date').value = recurrenceEnd ? recurrenceEnd.split(' ')[0] : ''; } else { document.getElementById('edit_recurrence_end_div').style.display = 'none'; document.getElementById('edit_recurrence_end_date').value = ''; }
        if (deadline) { let dateObj = new Date(deadline); dateObj.setMinutes(dateObj.getMinutes() - dateObj.getTimezoneOffset()); document.getElementById('edit_task_deadline').value = dateObj.toISOString().slice(0, 16); }
        
        window.fetchUsersForEdit(clientId, users);
        
        new bootstrap.Modal(document.getElementById('editTaskModal')).show();
    }

    function confirmAction(event, formElement, message, iconType = 'warning', confirmColor = '#d33') {
        event.preventDefault(); 
        Swal.fire({
            title: 'هل أنت متأكد؟', text: message, icon: iconType, showCancelButton: true,
            confirmButtonColor: confirmColor, cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، تأكيد!', cancelButtonText: 'إلغاء',
            customClass: { popup: 'rounded-4 shadow-lg border-0' }
        }).then((result) => { if (result.isConfirmed) formElement.submit(); });
    }
</script>
@endsection