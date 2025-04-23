@extends('dashboard.layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-12">
                <div class="card border-0">
                    <div class="card-header">
                        <h6 class="text-lg font-weight-semibold mb-0">Edit Work Flow</h6>
                    </div>
                    <form class="card-body" method="POST" action="{{ route('workflow.update', $flow->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Name
                                </x-forms.labels.basic>
                                <x-forms.input-label.basic 
                                    name="name" 
                                    required
                                    value="{{ old('name', $flow->name) }}"
                                    label="Workflow Name"
                                />
                            </div>
                            
                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Type
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="type" required>
                                    <option value="">Select Type</option>
                                    @foreach(\App\Enums\WorkFlow\WorkFlowType::cases() as $case)
                                        <option
                                            value="{{ $case->value }}"
                                            @selected(old('type', $flow->type) === $case->value)
                                        >
                                            {{ ucfirst($case->name) }}
                                        </option>
                                    @endforeach
                                </x-forms.select.basic>
                            </div>

                            <div class="col-12 pt-4 text-end">
                                <x-forms.buttons.primary class="float-end">
                                    Update
                                </x-forms.buttons.primary>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection