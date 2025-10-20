@extends('dashboard.layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <div class="fw-bold">{{ $association->name }}</div>
        </div>

        <div class="card my-3">
            <div class="card-body d-flex justify-content-between">
                <p><strong>{{ __('messages.start_date') }}:</strong> {{ $association->start_date }}</p>
                <p><strong>{{ __('messages.end_date') }}:</strong> {{ $association->end_date }}</p>
                <p><strong>{{ __('messages.per_day') }}:</strong> {{ $association->per_day }}</p>
                <p><strong>{{ __('messages.monthly_amount') }}:</strong> {{ $association->monthly_amount }}</p>
                <p><strong>{{ __('messages.total_members') }}:</strong> {{ $association->total_members }}</p>
                <p><strong>{{ __('messages.created_by') }}:</strong> {{ $association->creator?->name }}</p>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center my-3">
            <div class="fw-bold">{{ __('messages.members_list') }}</div>
            <button class="btn btn-outline-primary btn-sm radius-8" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                {{ __('messages.add_member') }}
            </button>
        </div>
        <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0">
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">{{ __('messages.client_name') }}</th>
                    <th class="text-center">{{ __('messages.payment_times') }}</th>
                    <th class="text-center">{{ __('messages.is_recevied') }}</th>
                    <th class="text-center">{{ __('messages.receive_date') }}</th>
                    <th class="text-center">{{ __('messages.payout_order') }}</th>
                    <th class="text-center">{{ __('messages.payment_status') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody id="membersTableBody">
                @foreach ($association->members as $member)
                {{-- @dd($member) --}}
                    <tr data-id="{{ $member->id }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $member->client->name }}</td>
                        <td>{{ $member->payments->count() }}</td>
                        <td>
                            @if($member->has_received)
                                {{ __('messages.yes') }}
                            @else
                                {{ __('messages.not_yet') }}
                            @endif
                        </td>
                        <td>{{ $member->receive_date }}</td>
                        <td>{{ $member->payout_order }}</td>
                        <td>
                            @php
                                $totalPaid = $member->payments->sum('amount');
                                $dueAmount = $association->monthly_amount;
                                $status = ($totalPaid >= $dueAmount) ? 'paid' : (($totalPaid > 0) ? 'pending' : 'late');
                            @endphp
                            <span
                                class="badge {{ $status == 'paid' ? 'bg-success' : ($status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                {{ __('messages.' . $status) }} ({{ $totalPaid }})
                                {{-- {{ __('messages.' . $status) }} ({{ $totalPaid }} / {{ $dueAmount }}) --}}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-between gap-1">
                                @if(!$member->has_received)
                                    <button data-id="{{ $member->id }}" data-client-name="{{ $member->client->name }}"
                                        data-amount="{{ $association->monthly_amount }}"
                                        class="pay-member btn btn-outline-primary btn-sm btn-sm radius-8">{{ __('messages.send_money') }}</button>
                                @else
                                    <button class="btn btn-outline-secondary btn-sm btn-sm radius-8"
                                        style="cursor: not-allowed">{{ __('messages.this_member_recevied') }}</button>
                                @endif

                                <button class="btn btn-outline-success btn-sm add-payment btn-sm radius-8"
                                    data-id="{{ $member->id }}" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                                    {{ __('messages.add_payment') }}
                                </button>
                                <button class="btn btn-outline-danger btn-sm delete-member btn-sm radius-8"
                                    data-id="{{ $member->id }}">
                                    {{ __('messages.delete') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pay Member Modal -->
        <div class="modal fade" id="payMemberModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="payMemberForm">
                    @csrf
                    <input type="hidden" name="member_id" id="pm_member_id">
                    <input type="hidden" name="amount" id="pm_amount">
                    <input type="hidden" name="commission" id="pm_commission" value="0">

                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="modal-title">{{ __('messages.send_money') }}: <span id="pm_member_name"></span></div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="pm_payment_way" class="form-label">{{ __('messages.select_payment_way') }}</label>
                                <select name="payment_way_id" id="pm_payment_way" class="form-select" required>
                                <option value="" disabled selected>{{ __('messages.select_payment_way') }}</option>
                                    @foreach($paymentWays as $pw)
                                        <option value="{{ $pw->id }}">{{ $pw->name }} ({{ $pw->type }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">

                            <label>{{ __('messages.commission') }}</label>
                            <input type="number" step="0.01" name="commission" id="payCommission" class="form-control"
                                required>
                            </div>

                            <div class="mb-2">
                                <small>{{ __('messages.amount') }}: <strong id="pm_amount_label"></strong></small>
                            </div>

                            <div id="pm_alert" class="alert alert-danger d-none" role="alert"></div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-outline-primary btn-sm btn-sm radius-8"> {{ __('messages.save') }}</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-sm radius-8" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>



        <!-- Add Member Modal -->
        <div class="modal fade" id="addMemberModal" tabindex="-1">
            <div class="modal-dialog">
                <form id="addMemberForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="modal-title">{{ __('messages.add_member_to_association') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <label>{{ __('messages.select_client') }}</label>
                            <select name="client_id" class="form-select" required>
                                <option value="" disabled selected>{{ __('messages.select_client') }}</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-outline-primary btn-sm radius-8">
                                {{ __('messages.save') }}
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm radius-8" data-bs-dismiss="modal">
                                {{ __('messages.close') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Payment Modal -->
        <div class="modal fade" id="addPaymentModal" tabindex="-1">
            <div class="modal-dialog">
                <form id="addPaymentForm">
                    @csrf
                    <input type="hidden" name="member_id" id="paymentMemberId">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="modal-title">{{ __('messages.add_payment') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <label>{{ __('messages.amount') }}</label>
                            <input type="number" name="amount" value="{{ $association->monthly_amount }}"
                                class="form-control" required placeholder="{{ __('messages.enter_amount') }}">
                            <label>{{ __('messages.payment_way') }}</label>

                            <select name="payment_way_id" id="payPaymentWay" class="form-control" required>

                                <option value="" disabled selected>{{ __('messages.select_payment_way') }}</option>

                                @foreach ($paymentWays as $way)
                                    <option value="{{ $way->id }}">{{ $way->name }}</option>
                                @endforeach
                            </select>
                            <label>{{ __('messages.commission') }}</label>
                            <input type="number" step="0.01" name="commission" id="payCommission" class="form-control"
                                required>
                            <label>{{ __('messages.payment_date') }}</label>
                            <input type="date" name="payment_date" class="form-control" required
                                value="{{ now()->format('Y-m-d') }}">
                            <label>{{ __('messages.status') }}</label>
                            <select name="status" class="form-control">
                                <option value="paid">{{ __('messages.paid') }}</option>
                                <option value="pending">{{ __('messages.pending') }}</option>
                                <option value="late">{{ __('messages.late') }}</option>
                            </select>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-outline-primary btn-sm">{{ __('messages.save') }}</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteMemberModal" tabindex="-1" aria-labelledby="deleteMemberModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-3 shadow">
                    <div class="modal-header">
                        <div class="modal-title" id="deleteMemberModalLabel">{{ __('messages.delete_member') }}</div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        {{ __('messages.are_you_sure_you_want_to_delete_this_member') }}
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="button" id="confirmDeleteMember"
                            class="btn btn-danger">{{ __('messages.delete') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Add Member
            $('#addMemberForm').submit(function (e) {
                e.preventDefault();
                $.post("{{ route('associations.addMember', $association->id) }}", $(this).serialize(), function (res) {
                    if (res.status) {
                        $('#addMemberModal').modal('hide');
                        showToast(res.message, 'success');
                        location.reload();
                    } else {
                        showToast(res.message, 'error');
                    }
                }).fail(function (xhr) {
                    showToast(xhr.responseJSON?.message || 'Something went wrong', 'error');
                });
            });

            // Add Payment
            $('.add-payment').click(function () {
                $('#paymentMemberId').val($(this).data('id'));
                $('#addPaymentModal').modal('show');
            });

            $('#addPaymentForm').submit(function (e) {
                e.preventDefault();
                $.post("{{ route('associations.addPayment', $association->id) }}", $(this).serialize(), function (res) {
                    if (res.status) {
                        $('#addPaymentModal').modal('hide');
                        showToast(res.message, 'success');
                        location.reload();
                    } else {
                        showToast(res.message, 'error');
                    }
                }).fail(function (xhr) {
                    showToast(xhr.responseJSON?.message || 'Something went wrong', 'error');
                });
            });

            // Delete Member
            let selectedMemberId = null;
            let selectedRow = null;

            $(document).on('click', '.delete-member', function () {
                selectedRow = $(this).closest('tr');
                selectedMemberId = selectedRow.data('id');
                const modal = new bootstrap.Modal(document.getElementById('deleteMemberModal'));
                modal.show();
            });

            $('#confirmDeleteMember').on('click', function () {
                if (!selectedMemberId) return;

                $.ajax({
                    url: `{{ url('dashboard/associations/' . $association->id . '/members') }}/${selectedMemberId}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (res) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteMemberModal'));
                        modal.hide();
                        if (res.status) {
                            showToast(res.message, 'success');
                            location.reload();
                        } else {
                            showToast(res.message, 'error');
                        }
                    },
                    error: function (xhr) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteMemberModal'));
                        modal.hide();
                        showToast(xhr.responseJSON?.message || 'Something went wrong.', 'error');
                    }
                });
            });

            $(document).on('click', '.pay-member', function () {
                const memberId = $(this).data('id');
                const memberName = $(this).data('client-name') || '—';

                const monthlyAmount = {{ $association->monthly_amount }};
                const totalMembers = {{ $association->members->count() }};
                const total = monthlyAmount * totalMembers;

                $('#pm_member_id').val(memberId);
                $('#pm_member_name').text(memberName);
                $('#pm_amount').val(total);
                $('#pm_amount_label').text(total.toLocaleString());

                $('#pm_payment_way').val('');
                $('#pm_alert').addClass('d-none').text('');

                const modalEl = document.getElementById('payMemberModal');
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            });


            $('#payMemberForm').submit(function (e) {
                e.preventDefault();

                const associationId = {{ $association->id }}; 
                const url = `{{ url('dashboard/associations') }}/${associationId}/pay-member`;
                const data = $(this).serialize();

                $.post(url, data)
                    .done(function (res) {
                        if (res.status) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('payMemberModal'));
                            modal.hide();
                            showToast(res.message ||  'success');
                            location.reload();
                        } else {
                            $('#pm_alert').removeClass('d-none').text(res.message ||'error');
                        }
                    })
                    .fail(function (err) {
                        const msg = err.responseJSON?.message || 'error';
                        $('#pm_alert').removeClass('d-none').text(msg);
                    });
            });
        });
    </script>
@endpush