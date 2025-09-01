@extends('dashboard.layouts.app')

@section('content')

    <div class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="max-w-464-px mx-auto w-100 p-4">

            <div>
                <a href="" class="mb-40 max-w-290-px">
             
                    
                        @if(config('app.client_name') == "Alkarim")
            
                          <img src="{{ asset('assets/images/alkarim.png') }}" alt="">
               @elseif(config('app.client_name') == "Upedia")
                         <img src="{{ asset('assets/images/logo.png') }}" alt="">
               @else
                 <img src="{{ asset('assets/images/tailors.png') }}" alt="site logo" class="light-logo">
    
               
            @endif
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
