@extends('dashboard.layouts.app')

@section('content')

    <div class="d-flex align-items-center justify-content-center">
        <div class="max-w-464-px mx-auto w-100 p-3">

            <div>
                <a href="" class="mt-5">
                    <img src="{{ asset('assets/images/1.png') }}" alt="">

                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

        </div>
    </div>
@endsection
