<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editId">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title fw-bold fs-5">{{ __('messages.edit_category') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>{{ __('messages.name') }}</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.parent') }}</label>
                        <select name="parent_id" id="editParent" class="form-control">
                            <option value="">{{ __('messages.none') }}</option>
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
