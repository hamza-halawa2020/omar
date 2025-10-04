<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editId">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title fw-bold fs-5">{{ __('messages.edit_product') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>{{ __('messages.name') }}</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                     <div class="mb-3">
                        <label>{{ __('messages.code') }}</label>
                        <input type="text" name="code" id="editCode" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.image') }}</label>
                        <input type="file" name="image" id="editImage" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.description') }}</label>
                        <textarea name="description" id="editDescription" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.purchase_price') }}</label>
                        <input type="number" name="purchase_price" id="editPurchasePrice" class="form-control" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.sale_price') }}</label>
                        <input type="number" name="sale_price" id="editSalePrice" class="form-control" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.stock') }}</label>
                        <input type="number" name="stock" id="editStock" class="form-control" required>
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
