@extends('admin.layouts.app')

@section('title', 'إنشاء Tenant جديد')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">إنشاء Tenant جديد</div>
            <div class="card-body">
                <form action="{{ route('admin.tenants.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">اسم الشركة</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">الدومين</label>
                        <input type="text" name="domain" class="form-control @error('domain') is-invalid @enderror"
                               value="{{ old('domain') }}" placeholder="example" required>
                        @error('domain')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">إنشاء</button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
