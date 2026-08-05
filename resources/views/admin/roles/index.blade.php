@extends('admin.layouts.app')

@section('title', 'إدارة الأدوار - ' . $tenant->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none small">
            <iconify-icon icon="solar:arrow-right-outline"></iconify-icon> الرئيسية
        </a>
        <h4 class="fw-bold mb-0 mt-1">أدوار: {{ $tenant->name }}</h4>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createRoleModal">
        <iconify-icon icon="mingcute:add-line" class="me-1"></iconify-icon>
        إضافة دور
    </button>
</div>

<div class="row g-4">
    @forelse ($roles as $role)
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">{{ $role->name }}</span>
                    <form action="{{ route('admin.tenants.roles.destroy', [$tenant, $role->id]) }}" method="POST"
                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2">
                            <iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon>
                        </button>
                    </form>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.tenants.roles.permissions', [$tenant, $role->id]) }}" method="POST">
                        @csrf
                        <div class="row row-cols-1 g-1 mb-3">
                            @foreach ($permissions as $permission)
                                <div class="col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->name }}"
                                               id="perm_{{ $role->id }}_{{ $permission->id }}"
                                               @checked($role->permissions->contains('name', $permission->name))>
                                        <label class="form-check-label small" for="perm_{{ $role->id }}_{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                            حفظ الصلاحيات
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info">لا توجد أدوار لهذا الـ tenant حتى الآن.</div>
        </div>
    @endforelse
</div>

{{-- Create Role Modal --}}
<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.tenants.roles.store', $tenant) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة دور جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">اسم الدور</label>
                    <input type="text" name="name" class="form-control" required placeholder="مثال: محاسب">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary btn-sm">إنشاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
