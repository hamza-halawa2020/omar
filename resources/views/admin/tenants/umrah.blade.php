@extends('admin.layouts.app')
@section('title', __('umrah.umrah_staff_roles'))
@section('content')
    <div>{{ __('umrah.company_umrah') }} — {{ $tenant->name }}</div>
    <p>{{ __('umrah.umrah_staff_help') }}</p>
    <p><a href="{{ route('admin.tenants.users.create', $tenant) }}"
            class="btn btn-primary">{{ __('umrah.add_employee') }}</a></p>
    @if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    @foreach($users as $user)
        <div class="admin-panel p-3 mb-3">
            <div>{{ $user->name }} — {{ $user->email }}</div>
            <form method="POST" action="{{ route('admin.tenants.umrah.user', [$tenant, $user]) }}">@csrf @method('PUT')
                <label class="form-label" for="role-{{ $user->id }}">{{ __('umrah.employee_umrah_role') }}</label>
                <select id="role-{{ $user->id }}" name="umrah_role" class="form-select">
                    <option value="">{{ __('umrah.basic_viewer') }}</option>@foreach($roles as $role)
                        <option value="{{ $role }}" @selected(($userRoles[$user->id] ?? null) === $role)>
                            {{ __('umrah.role_' . $role) }}
                    </option>@endforeach
                </select>
                <button class="btn btn-primary mt-3">{{ __('umrah.save_employee_role') }}</button>
            </form>
        </div>
    @endforeach
@endsection