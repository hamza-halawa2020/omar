@extends('dashboard.layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-12">
                <div class="card border-0">
                    <div class="card-header">
                        <h6 class="text-lg font-weight-semibold mb-0">Edit call status</h6>
                    </div>
                    <form class="card-body" method="POST" action="{{ route('call_status.update', $call->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Name
                                </x-forms.labels.basic>
                                <x-forms.input-label.basic name="name" required value="{{ old('name', $call->name) }}"
                                    label="Workflow Name" />
                            </div>

                            <div class="col-md-6">
                                <x-forms.labels.basic>
                                    Workflow
                                </x-forms.labels.basic>
                                <x-forms.select.basic name="workflow_id" required>
                                    <option value="">Select Workflow</option>
                                    @foreach ($types as $workflow)
                                        <option value="{{ $workflow->id }}" @selected(old('workflow_id', $call->workflow_id) == $workflow->id)>
                                            {{ $workflow->name }}
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
