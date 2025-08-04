@extends('dashboard.layouts.app')

@section('content')
@include('components.alert')

<div class="container mt-4">
    {{-- @include('dashboard.layouts.flash') --}}

    <h1>Create Permission</h1>

    <form action="{{ route('permissions.store') }}" method="POST" class="mt-3">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Permission Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Enter permission name">
        </div>

        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>
@endsection
