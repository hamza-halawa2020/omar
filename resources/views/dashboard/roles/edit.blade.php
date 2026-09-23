@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div>{{ __('messages.edit_role') }}</div>
        <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>{{ __('messages.name') }}</label>
                <input type="text" name="name" value="{{ old('name', $role->name) }}" class="form-control" required>
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="d-block mb-2">{{ __('messages.permissions') }}</label>
                <select name="permissions[]" class="form-control js-permissions-select" multiple>
                    @foreach ($rolePermissions as $permissionName)
                        <option value="{{ $permissionName }}" selected>{{ __('messages.' . $permissionName) }}</option>
                    @endforeach
                </select>
            </div>

            <button class="btn btn-outline-success btn-sm radius-8">{{ __('messages.update') }}</button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            $('.js-permissions-select').select2({
                width: '100%',
                placeholder: '{{ __('messages.permissions') }}',
                allowClear: true,
                ajax: {
                    url: '{{ route('roles.permissions.list') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term || '',
                            limit: 100
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response.data || []
                        };
                    },
                    cache: true
                }
            });
        });
    </script>
@endpush
