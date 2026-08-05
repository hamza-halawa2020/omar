@extends('admin.layouts.app')

@section('title', 'مستخدمو ' . $tenant->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">مستخدمو: {{ $tenant->name }}</h4>
        <small class="text-muted">{{ $tenant->domain }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.tenants.users.create', $tenant) }}" class="btn btn-primary btn-sm">
            <iconify-icon icon="mingcute:add-line" class="me-1"></iconify-icon>
            إضافة مستخدم
        </a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">← رجوع</a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>تاريخ الإنشاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->created_at->format('Y-m-d') }}</td>
                            <td>
                                <form action="{{ route('admin.tenants.users.destroy', [$tenant, $user]) }}"
                                      method="POST"
                                      onsubmit="return confirm('حذف المستخدم {{ $user->name }}؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <iconify-icon icon="solar:trash-bin-minimalistic-outline"></iconify-icon>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">لا يوجد مستخدمون لهذا الـ tenant.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
