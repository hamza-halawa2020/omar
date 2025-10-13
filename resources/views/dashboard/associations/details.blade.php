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
                    <th class="text-center">{{ __('messages.receive_date') }}</th>
                    <th class="text-center">{{ __('messages.payout_order') }}</th>
                    <th class="text-center">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody id="membersTableBody">
                @foreach ($association->members as $member)
                    <tr data-id="{{ $member->id }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $member->client->name }}</td>
                        <td>{{ $member->receive_date }}</td>
                        <td>{{ $member->payout_order }}</td>
                        <td>
                            <button class="btn btn-outline-danger btn-sm delete-member">
                                {{ __('messages.delete') }}
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

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
                        class="btn btn-danger">{{__('messages.delete') }}</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#addMemberForm').submit(function (e) {
                e.preventDefault();

                $.post("{{ route('associations.addMember', $association->id) }}", $(this).serialize(), function (res) {
                    if (res.status) {
                        $('#addMemberModal').modal('hide');
                        showToast(res.message, 'success');

                        location.reload();

                        // let newRow = `
                        //         <tr>
                        //             <td>${$('#membersTableBody tr').length + 1}</td>
                        //             <td>${res.data.client.name}</td>
                        //             <td>${res.data.receive_date}</td>
                        //             <td>${res.data.payout_order}</td>
                        //         </tr>
                        //     `;
                        // $('#membersTableBody').append(newRow);
                        $('#addMemberForm')[0].reset();
                    } else {
                        showToast(res.message, 'error');
                    }
                }).fail(function (xhr) {
                    showToast(xhr.responseJSON?.message || 'Something went wrong', 'error');
                });
            });
        });



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
                        selectedRow.remove();

                        $('#membersTableBody tr').each(function (index) {
                            $(this).find('td:first').text(index + 1);
                        });
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

    </script>
@endpush