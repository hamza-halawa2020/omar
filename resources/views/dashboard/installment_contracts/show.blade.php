@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <div>{{ __('messages.installment_contract_details') }}</div>
         
        </div>

        <!-- Client & Product Info -->
        <div class="card mb-3">
            <div class="card-header fw-bold">{{ __('messages.client_and_product_info') }}</div>
            <div class="card-body row">
                <div class="col-md-6">
                    <p><strong>{{ __('messages.client') }}:</strong> {{ $contract->client->name }}({{ $contract->client->phone_number }})</p>
                    <p><strong>{{ __('messages.product') }}:</strong> {{ $contract->product->name }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>{{ __('messages.created_by') }}:</strong> {{ $contract->creator->name }}</p>
                    <p><strong>{{ __('messages.start_date') }}:</strong> {{ $contract->start_date }}</p>
                </div>
            </div>
        </div>

        <!-- Contract Details -->
        <div class="card mb-3">
            <div class="card-header fw-bold">{{ __('messages.contract_details') }}</div>
            <div class="card-body row">
                <div class="col-md-3"><strong>{{ __('messages.product_price') }}:</strong> {{ $contract->product_price }}</div>
                <div class="col-md-3"><strong>{{ __('messages.down_payment') }}:</strong> {{ $contract->down_payment }}</div>
                <div class="col-md-3"><strong>{{ __('messages.remaining_amount') }}:</strong>{{ $contract->remaining_amount }}</div>
                <div class="col-md-3"><strong>{{ __('messages.interest_rate') }}:</strong> {{ $contract->interest_rate }}%</div>
                <div class="col-md-3"><strong>{{ __('messages.interest_amount') }}:</strong>{{ $contract->interest_amount }}</div>
                <div class="col-md-3"><strong>{{ __('messages.total_amount') }}:</strong> {{ $contract->total_amount }}</div>
                <div class="col-md-6"><strong>{{ __('messages.installment_count_left') }}:</strong>{{ $contract->remaining_installments }}</div>
                <div class="col-md-6"><strong>{{ __('messages.installment_amount') }}:</strong>{{ $contract->installment_amount }}</div>
            </div>
        </div>

        <!-- Installments -->
        <div class="card mb-3">
            <div class="card-header fw-bold">{{ __('messages.installments') }}</div>
            <div class="card-body">
                <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">{{ __('messages.due_date') }}</th>
                            <th class="text-center">{{ __('messages.required_amount') }}</th>
                            <th class="text-center">{{ __('messages.paid_amount') }}</th>
                            <th class="text-center">{{ __('messages.status') }}</th>
                            <th class="text-center">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contract->installments as $i => $inst)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $inst->due_date->format('Y-m-d') }}</td>
                                <td>{{ $inst->required_amount }}</td>
                                <td>{{ $inst->paid_amount }}</td>
                                <td>
                                    @if ($inst->status == 'paid')
                                        <span class="badge bg-success">{{ __('messages.paid') }}</span>
                                    @elseif($inst->status == 'late')
                                        <span class="badge bg-danger">{{ __('messages.late') }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ __('messages.pending') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($inst->status != 'paid')
                                        <button class="btn btn-outline-success btn-sm radius-8 payBtn"
                                            data-id="{{ $inst->id }}"
                                            data-amount="{{ $inst->required_amount - $inst->paid_amount }}">
                                            {{ __('messages.pay') }}
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @if ($inst->payments->count())
                                <tr>
                                    <td colspan="6" class="text-start">
                                        <strong>{{ __('messages.payments') }}:</strong>
                                        <ul>
                                            @foreach ($inst->payments as $pay)
                                                <li>{{ $pay->payment_date->format('Y-m-d') }} - {{ $pay->amount }}
                                                    ({{ $pay->payer->name ?? '' }})</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    @include('dashboard.installment_contracts.pay')
@endsection

@push('scripts')
    <script>
        $(document).on('click', '.payBtn', function() {
            $('#payInstallmentId').val($(this).data('id'));
            $('#payAmount').val($(this).data('amount'));
            $('#payModal').modal('show');
        });

        $('#payForm').submit(function(e) {
            e.preventDefault();
            $.post("{{ route('installments.pay') }}", $(this).serialize(), function(res) {
                if (res.status) {
                    location.reload();
                } else {
                    showToast(res.message, 'error');
                }
            }).fail(function(xhr) {
                showToast(xhr.responseJSON?.message || 'Something went wrong', 'error');
            });
        });
    </script>
@endpush
