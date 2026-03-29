@extends('layouts.app')

@section('title', 'إعدادات الحساب')

@section('content')
<div class="row justify-content-center mb-4 mt-3">
    <div class="col-md-8 text-center">
        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">
            {{ mb_substr(auth()->user()->username, 0, 1, 'UTF-8') }}
        </div>
        <h4 class="fw-bold text-dark">{{ auth()->user()->username }}</h4>
        <span class="badge bg-{{ auth()->user()->role == 'admin' ? 'danger' : 'secondary' }} rounded-pill px-3 py-2">
            {{ auth()->user()->role == 'admin' ? 'مدير النظام' : 'موظف' }}
        </span>
    </div>
</div>

<div class="row justify-content-center g-4">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom pt-3 pb-2">
                <h6 class="fw-bold text-dark mb-0"><i class="fas fa-user-edit text-primary me-2"></i> تغيير اسم المستخدم</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profile.username') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted px-1">اسم المستخدم الجديد</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="username" class="form-control" value="{{ auth()->user()->username }}" required autocomplete="off">
                        </div>
                        @error('username')
                            <small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">
                        حفظ الاسم <i class="fas fa-save ms-1"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom pt-3 pb-2">
                <h6 class="fw-bold text-dark mb-0"><i class="fas fa-shield-alt text-success me-2"></i> تغيير كلمة المرور</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted px-1">كلمة المرور الحالية</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-unlock-alt"></i></span>
                            <input type="password" name="old_password" class="form-control" placeholder="أدخل الباسورد القديم" required>
                        </div>
                        @error('old_password')
                            <small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted px-1">كلمة المرور الجديدة</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="password" name="new_password" class="form-control" placeholder="6 أحرف أو أرقام على الأقل" minlength="6" required>
                        </div>
                        @error('new_password')
                            <small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-success w-100 fw-bold shadow-sm">
                        تحديث كلمة المرور <i class="fas fa-lock ms-1"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection