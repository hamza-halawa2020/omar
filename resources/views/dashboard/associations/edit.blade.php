<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editId">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title fw-bold fs-5">{{ __('messages.edit_association') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>{{ __('messages.name') }}</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>{{ __('messages.total_members') }}</label>
                        <input type="number" name="total_members" id="editTotalMembers" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>{{ __('messages.monthly_amount') }}</label>
                        <input type="number" step="0.01" name="monthly_amount" id="editMonthlyAmount"
                            class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>{{ __('messages.start_date') }}</label>
                        <input type="date" name="start_date" id="editStartDate" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>{{ __('messages.end_date') }}</label>
                        <input type="date" name="end_date" id="editEndDate" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>{{ __('messages.status') }}</label>
                        <select name="status" id="editStatus" class="form-control">
                            <option value="active">{{ __('messages.active') }}</option>
                            <option value="completed">{{ __('messages.completed') }}</option>
                            <option value="paused">{{ __('messages.paused') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit"
                        class="btn btn-outline-primary btn-sm radius-8">{{ __('messages.update') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm radius-8"
                        data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
