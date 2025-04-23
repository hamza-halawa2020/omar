@php
    use App\Enums\Calls\RelatedToType;
@endphp
@extends('dashboard.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="text-lg fw-semibold mb-0">{{ $title }} - {{ $subTitle }}</h6>
                <div class="btn-group">
                    <a href="{{ route('calls.index', ['view' => 'kanban']) }}"
                       class="btn btn-outline-primary {{ request('view', 'kanban') === 'kanban' ? 'active' : '' }}">
                        Kanban View
                    </a>
                    <a href="{{ route('calls.index', ['view' => 'list']) }}"
                       class="btn btn-outline-primary {{ request('view', 'kanban') === 'list' ? 'active' : '' }}">
                        List View
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('dashboard.crud.calls.partials.list')
            </div>
        </div>
    </div>
@endsection