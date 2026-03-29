@extends('layouts.app')

@section('title', 'إدارة الموظفين')

@section('content')
<style>
    /* تنسيق إضافي لتحسين شكل الـ Hover على العملاء داخل النافذة */
    .hover-bg-light:hover { background-color: #f8f9fa; border-radius: 6px; }
</style>

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h4 class="fw-bold text-dark mb-0">
            <i class="fas fa-user-tie text-primary me-2"></i> إدارة الموظفين والصلاحيات
        </h4>
    </div>
    <div class="col-md-6 text-end">
        <button data-bs-toggle="modal" data-bs-target="#addUserModal" class="btn btn-primary fw-bold shadow-sm rounded-pill px-4">
            <i class="fas fa-plus me-1"></i> إضافة موظف جديد
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-light">
                    <tr>
                        <th class="py-3">اسم الموظف</th>
                        <th>الصلاحية</th>
                        <th>عملاء بعهدته</th>
                        <th>مهام معلقة / منجزة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td class="fw-bold text-dark">{{ $user->username }}</td>
                            <td>
                                @if($user->role == 'admin')
                                    <span class="badge bg-danger rounded-pill px-3">مدير نظام</span>
                                @else
                                    <span class="badge bg-info text-dark rounded-pill px-3">موظف</span>
                                @endif
                            </td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge bg-success rounded-pill px-3" title="يرى جميع العملاء تلقائياً"><i class="fas fa-infinity"></i> صلاحية شاملة</span>
                                @else
                                    <span class="badge bg-primary rounded-pill px-3">{{ $user->assigned_clients_count }} عميل</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark me-1" title="معلقة">{{ $user->pending_tasks_count }} <i class="fas fa-hourglass-half"></i></span>
                                <span class="badge bg-success" title="منجزة">{{ $user->completed_tasks_count }} <i class="fas fa-check"></i></span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    @if($user->role !== 'admin')
                                        <button class="btn btn-sm btn-outline-primary rounded-circle" title="تخصيص العملاء" style="width: 32px; height: 32px; padding: 0;" data-bs-toggle="modal" data-bs-target="#assignClientsModal{{ $user->id }}">
                                            <i class="fas fa-users-cog"></i>
                                        </button>
                                    @else
                                        <div style="width: 32px; height: 32px;"></div>
                                    @endif
                                        <button class="btn btn-sm btn-outline-success rounded-circle" title="تعديل الموظف" style="width: 32px; height: 32px; padding: 0;" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                    @if($user->id !== auth()->id() && $user->role !== 'admin')
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="confirmAction(event, this, 'تأكيد حذف الموظف نهائياً؟', 'error', '#dc3545');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" style="width: 32px; height: 32px; padding: 0;"><i class="fas fa-trash"></i></button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content rounded-4 border-0 shadow-lg">
                                    <div class="modal-header bg-light border-0 py-3">
                                        <h5 class="modal-title fw-bold"><i class="fas fa-user-edit text-success me-2"></i> تعديل بيانات: {{ $user->username }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">اسم المستخدم</label>
                                                <input type="text" name="username" value="{{ $user->username }}" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">كلمة المرور الجديدة <span class="text-danger small">(اتركه فارغاً إذا لا تريد تغييره)</span></label>
                                                <input type="password" name="password" class="form-control" placeholder="****">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">الصلاحية</label>
                                                <select name="role" class="form-select">
                                                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>موظف عادي</option>
                                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>مدير نظام</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 bg-light">
                                            <button type="submit" class="btn btn-success rounded-pill px-4 shadow">حفظ التعديلات</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="assignClientsModal{{ $user->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <div class="modal-content rounded-4 border-0 shadow-lg">
                                    <div class="modal-header bg-light border-0 py-3">
                                        <h5 class="modal-title fw-bold"><i class="fas fa-users-cog text-primary me-2"></i> صلاحيات وصول: {{ $user->username }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('users.assign_clients', $user->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body p-4">
                                            
                                            <div class="alert alert-info small border-0 shadow-sm py-2 mb-3">
                                                <i class="fas fa-info-circle me-1"></i> هذه الشاشة توضح جميع العملاء النشطين وحالة وصول الموظف إليهم بناءً على إعدادات الرؤية الخاصة بكل عميل.
                                            </div>
                                            
                                            @php 
                                                // جلب كل العملاء النشطين
                                                $allActiveClients = \App\Models\Client::where('status', 'active')->orderBy('name')->get();
                                                
                                                // جلب الأيديهات المربوطة بالموظف (تأكد أن اسم العلاقة في موديل اليوزر هو assignedClients أو clients)
                                                // لو العلاقة اسمها clients غيرها لـ: $user->clients->pluck('id')->toArray()
                                                $userClientIds = $user->assignedClients ? $user->assignedClients->pluck('id')->toArray() : []; 
                                            @endphp
                                            
                                            <div class="border rounded-3 p-3 bg-white shadow-sm" style="max-height: 300px; overflow-y: auto;">
                                                @forelse($allActiveClients as $c)
                                                    <div class="d-flex align-items-center justify-content-between mb-2 border-bottom pb-2 hover-bg-light px-1">
                                                        
                                                        @if($c->visibility == 'all')
                                                            <div class="form-check mb-0">
                                                                <input class="form-check-input bg-success border-success" type="checkbox" checked disabled>
                                                                <label class="form-check-label small text-dark fw-bold opacity-75">
                                                                    {{ Str::limit($c->name, 30) }}
                                                                </label>
                                                            </div>
                                                            <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill" style="font-size: 0.65rem;">متاح للجميع</span>
                                                        
                                                        @elseif($c->visibility == 'admins_only')
                                                            <div class="form-check mb-0">
                                                                <input class="form-check-input bg-secondary border-secondary opacity-50" type="checkbox" disabled>
                                                                <label class="form-check-label small text-muted fw-bold text-decoration-line-through">
                                                                    {{ Str::limit($c->name, 30) }}
                                                                </label>
                                                            </div>
                                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill" style="font-size: 0.65rem;"><i class="fas fa-lock me-1"></i> للإدارة فقط</span>
                                                        
                                                        @else
                                                            <div class="form-check mb-0">
                                                                <input class="form-check-input border-primary" style="cursor: pointer;" type="checkbox" name="clients[]" value="{{ $c->id }}" id="u_{{ $user->id }}_c_{{ $c->id }}" {{ in_array($c->id, $userClientIds) ? 'checked' : '' }}>
                                                                <label class="form-check-label small text-primary fw-bold" style="cursor: pointer;" for="u_{{ $user->id }}_c_{{ $c->id }}">
                                                                    {{ Str::limit($c->name, 30) }}
                                                                </label>
                                                            </div>
                                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary rounded-pill" style="font-size: 0.65rem;">قابل للتخصيص</span>
                                                        @endif

                                                    </div>
                                                @empty
                                                    <div class="text-muted small text-center py-3">
                                                        <i class="fas fa-folder-open fa-2x mb-2 opacity-25"></i><br>
                                                        لا يوجد عملاء نشطين في النظام حالياً.
                                                    </div>
                                                @endforelse
                                            </div>
                                            
                                        </div>
                                        <div class="modal-footer border-0 bg-light">
                                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">حفظ التخصيص</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-light border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-plus text-primary me-2"></i> إضافة موظف جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">اسم المستخدم (للدخول)</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">كلمة المرور</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">الصلاحية</label>
                        <select name="role" class="form-select" required>
                            <option value="user">موظف عادي</option>
                            <option value="admin">مدير نظام</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">إضافة الموظف</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection