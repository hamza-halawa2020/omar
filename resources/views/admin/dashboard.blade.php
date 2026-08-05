@extends('admin.layouts.app')

@section('title', 'لوحة تحكم المشرف العام')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">إدارة الـ Tenants</h4>
    <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary btn-sm">
        <iconify-icon icon="mingcute:add-line" class="me-1"></iconify-icon>
        إضافة Tenant جديد
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الدومين</th>
                        <th>تاريخ الإنشاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tenants as $tenant)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $tenant->name }}</td>
                            <td><span class="badge bg-secondary">{{ $tenant->domain }}</span></td>
                            <td>{{ $tenant->created_at->format('Y-m-d') }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    {{-- Roles --}}
                                    <a href="{{ route('admin.tenants.roles.index', $tenant) }}"
                                       class="btn btn-outline-primary btn-sm" title="الأدوار">
                                        <iconify-icon icon="solar:shield-keyhole-outline"></iconify-icon>
                                    </a>
                                    {{-- Users --}}
                                    <a href="{{ route('admin.tenants.users.index', $tenant) }}"
                                       class="btn btn-outline-secondary btn-sm" title="المستخدمون">
                                        <iconify-icon icon="solar:users-group-rounded-outline"></iconify-icon>
                                    </a>
                                    {{-- Delete --}}
                                    <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الـ tenant؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="حذف">
                                            <iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">لا يوجد tenants حتى الآن.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
