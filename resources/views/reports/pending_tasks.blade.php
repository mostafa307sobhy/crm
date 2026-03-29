@extends('layouts.app') 
@section('title', $isOverdue ? 'سجل المهام المتأخرة' : 'سجل المهام المعلقة')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0">
            @if($isOverdue)
                <i class="fas fa-exclamation-triangle text-danger me-2 pulse-danger"></i> سجل المهام المتأخرة (SLA)
            @else
                <i class="fas fa-tasks text-warning me-2"></i> سجل المهام المعلقة
            @endif
        </h3>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary rounded-pill shadow-sm">
            <i class="fas fa-arrow-right me-1"></i> العودة للتقارير
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('pending_tasks.index') }}" method="GET" class="row g-3 align-items-end">
                @if($isOverdue) <input type="hidden" name="filter" value="overdue"> @endif
                
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">فلترة بالموظف</label>
                    <select name="user_id" class="form-select border-light shadow-sm">
                        <option value="">-- جميع الموظفين --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->username }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-muted">فلترة بالعميل</label>
                    <select name="client_id" class="form-select border-light shadow-sm">
                        <option value="">-- جميع العملاء --</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm fw-bold"><i class="fas fa-filter me-1"></i> بحث</button>
                    @if(request()->has('user_id') || request()->has('client_id'))
                        <a href="{{ route('pending_tasks.index', $isOverdue ? ['filter'=>'overdue'] : []) }}" class="btn btn-light border text-danger shadow-sm" title="إلغاء الفلاتر"><i class="fas fa-times"></i></a>
                    @endif
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
                        @forelse($pendingTasks as $task)
                            @php 
                                $isTaskOverdue = \Carbon\Carbon::parse($task->deadline)->isPast(); 
                            @endphp
                            <tr>
                                <td class="px-4 text-start">
                                    <span class="fw-bold text-dark d-block mb-1">{{ \Illuminate\Support\Str::limit($task->task_desc, 60) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('clients.show', $task->client_id) }}" class="text-decoration-none fw-bold text-primary hover-zoom d-inline-block">
                                        <i class="fas fa-building me-1 opacity-50"></i> {{ $task->client->name ?? 'عميل محذوف' }}
                                    </a>
                                </td>
                                <td>
                                    @foreach($task->assignedUsers as $u)
                                        <span class="badge bg-secondary mb-1"><i class="fas fa-user me-1 opacity-50"></i> {{ $u->username }}</span><br>
                                    @endforeach
                                </td>
                                <td>
                                    @if($task->priority == 'high') <span class="badge bg-danger">عاجلة جداً</span>
                                    @elseif($task->priority == 'medium') <span class="badge bg-warning text-dark">متوسطة</span>
                                    @else <span class="badge bg-info text-dark">عادية</span> @endif
                                </td>
                                <td>
                                    <div class="fw-bold {{ $isTaskOverdue ? 'text-danger' : 'text-dark' }}">
                                        <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($task->deadline)->format('Y-m-d') }}<br>
                                        <small><i class="far fa-clock me-1 mt-1"></i> {{ \Carbon\Carbon::parse($task->deadline)->format('h:i A') }}</small>
                                    </div>
                                    @if($isTaskOverdue)
                                        <span class="badge bg-danger mt-1 blink-hard">متأخرة!</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-5 text-center text-muted">
                                    <i class="fas fa-check-double fa-3x mb-3 text-success opacity-50"></i>
                                    <h5>لا توجد مهام معلقة تطابق بحثك</h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($pendingTasks->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-center">
                    {{ $pendingTasks->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>
<style>
    .blink-hard { animation: blinker 1s linear infinite; }
    @keyframes blinker { 50% { opacity: 0; } }
</style>
@endsection