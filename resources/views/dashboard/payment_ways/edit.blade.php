<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editId">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title fw-bold fs-5">Edit Payment Way</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <label>Category</label>
                    <select name="category_id" id="editCategorySelect" class="form-control" required>
                        <option value="">Select Category</option>
                        @foreach ($categories as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>

                    <label>Sub Category</label>
                    <select name="sub_category_id" id="editSubCategorySelect" class="form-control">
                        <option value="">Select Sub Category</option>
                    </select>

                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Type</label>
                        <select name="type" id="editType" class="form-control">
                            <option value="cash">Cash</option>
                            <option value="wallet">Wallet</option>
                            <option value="balance_machine">Balance Machine</option>
                        </select>
                    </div>

                    <div class="mb-3 phone_limit_group_edit">
                        <label>Phone Number</label>
                        <input type="text" name="phone_number" id="editPhone" class="form-control">
                    </div>

                    <div class="mb-3 phone_limit_group_edit">
                        <label>Send Limit</label>
                        <input type="number" name="send_limit" id="editSendLimit" class="form-control">
                    </div>

                    <div class="mb-3 phone_limit_group_edit">
                        <label>Receive Limit</label>
                        <input type="number" name="receive_limit" id="editReceiveLimit" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Balance</label>
                        <input type="number" name="balance" id="editBalance" class="form-control">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-primary btn-sm radius-8">Update</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm radius-8"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
