@extends('dashboard.layouts.app')

@section('content')
@include('components.alert')

<div class="container mt-4">
    {{-- @include('dashboard.layouts.flash') --}}

    <h1>Edit Permission</h1>

    <form action="{{ route('permissions.update', $permission->id) }}" method="POST" class="mt-3">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Permission Name</label>
            <input type="text" name="name" id="name" value="{{ $permission->name }}" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Update</button>
    </form>
</div>
@endsection
