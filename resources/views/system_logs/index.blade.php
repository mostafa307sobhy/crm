@extends('layouts.app')

@section('title', 'سجل الرقابة النظامية')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h4 class="fw-bold text-dark mb-0">
            <i class="fas fa-shield-alt text-danger me-2"></i> سجل الرقابة النظامية
        </h4>
        <p class="text-muted small mt-1 mb-0">مراقبة حركات الموظفين والأحداث</p>
    </div>
    <div class="col-md-6 text-end">
        <form action="{{ route('system_logs.index') }}" method="GET" class="input-group shadow-sm rounded-3" style="max-width: 350px; margin-left: 0; margin-right: auto;">
    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-0" placeholder="ابحث في جميع السجلات..." required>
    <button type="submit" class="btn btn-primary px-3"><i class="fas fa-search"></i></button>
    @if(request('search'))
        <a href="{{ route('system_logs.index') }}" class="btn btn-danger px-3" title="إلغاء البحث"><i class="fas fa-times"></i></a>
    @endif
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
                    @forelse($logs as $log)
                        @php
                            // تلوين الشارة حسب نوع الحركة (نفس اللوجيك القديم)
                            $type = strtoupper($log->action_type);
                            $badgeClass = 'bg-secondary';
                            if (Str::contains($type, ['DELETE', 'CLEAR', 'ERROR'])) $badgeClass = 'bg-danger';
                            elseif (Str::contains($type, ['ADD', 'UPLOAD', 'COMPLETE'])) $badgeClass = 'bg-success';
                            elseif (Str::contains($type, ['UPDATE', 'CHANGE', 'TOGGLE'])) $badgeClass = 'bg-warning text-dark';
                            elseif (Str::contains($type, ['LOGIN', 'LOGOUT'])) $badgeClass = 'bg-info text-dark';
                        @endphp
                        <tr class="log-row">
                            <td class="px-4 fw-bold text-dark">
                                <i class="fas fa-user-circle text-muted me-1"></i> 
                                {{ $log->user ? $log->user->username : 'نظام/محذوف' }}
                            </td>
                            <td>
                                <span class="badge {{ $badgeClass }} rounded-pill px-3">
                                    {{ $type }}
                                </span>
                            </td>
                            <td class="text-dark log-details" style="max-width: 300px;">
                                {{ $log->action_details }}
                            </td>
                            <td class="text-muted small" dir="ltr">
                                {{ $log->ip_address ?? '---' }}
                            </td>
                            <td class="text-muted small" dir="ltr">
                                {{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d') }} <br>
                                <span class="fw-bold">{{ \Carbon\Carbon::parse($log->created_at)->format('h:i A') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fas fa-history fa-3x mb-3 text-secondary opacity-50"></i>
                                <p class="mb-0 fw-bold">لا توجد سجلات حالية.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer bg-white border-top-0 d-flex justify-content-center pt-4 pb-3" dir="ltr">
            {{ $logs->withQueryString()->links() }}
        </div>

    </div>
</div>
@endsection

@section('scripts')

@endsection