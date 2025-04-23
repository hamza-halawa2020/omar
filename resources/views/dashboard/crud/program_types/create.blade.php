@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-12">
                <div class="card border-0">
                    <div class="card-header">
                        <h6 class="text-lg font-weight-semibold mb-0">Create program type</h6>
                    </div>
                    <form class="card-body" method="POST" action="{{ route('program_types.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-12">
                                <x-forms.input-label.basic name="name" required value="{{ old('name') }}">
                                    Name
                                </x-forms.input-label.basic>
                            </div>

                            <div class="col-12 pt-4 text-end">
                                <x-forms.buttons.primary class="float-end">
                                    Submit
                                </x-forms.buttons.primary>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
