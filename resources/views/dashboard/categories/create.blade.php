<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="createForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title fw-bold fs-5">Create Category</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Parent</label>
                        <select name="parent_id" class="form-control" id="parentSelect">
                            <option value="">None</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-primary btn-sm radius-8">Save</button>
                    <button type="button" class="btn btn-btn btn-outline-secondary btn-sm radius-8"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
