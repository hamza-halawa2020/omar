@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div>{{ __('messages.create_role') }}</div>
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>{{ __('messages.name') }}</label>
                <input type="text" name="name" class="form-control" required>
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="d-block mb-2">{{ __('messages.permissions') }}</label>
                <select name="permissions[]" class="form-control js-permissions-select" multiple></select>
            </div>

            <button class="btn btn-outline-success btn-sm radius-8">{{ __('messages.save') }}</button>
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
