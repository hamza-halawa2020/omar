<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editId">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title fw-bold fs-5">{{ __('messages.edit_installment') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row">

                    <!-- Client -->
                    <div class="mb-3 col-md-6">
                        <label>{{ __('messages.client') }}</label>
                        <select name="client_id" id="editClientId" class="form-control" required>
                            <option value="">{{ __('messages.choose_client') }}</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Product -->
                    <div class="mb-3 col-md-6">
                        <label>{{ __('messages.product') }}</label>
                        <select name="product_id" id="editProductId" class="form-control" required>
                            <option value="">{{ __('messages.choose_product') }}</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Product Price -->
                    <div class="mb-3 col-md-6">
                        <label>{{ __('messages.product_price') }}</label>
                        <input type="number" name="product_price" id="editProductPrice" class="form-control"
                            step="0.01" required>
                    </div>

                    <!-- Down Payment -->
                    <div class="mb-3 col-md-6">
                        <label>{{ __('messages.down_payment') }}</label>
                        <input type="number" name="down_payment" id="editDownPayment" class="form-control"
                            step="0.01">
                    </div>

                    <!-- Interest Rate -->
                    <div class="mb-3 col-md-6">
                        <label>{{ __('messages.interest_rate') }}</label>
                        <input type="number" name="interest_rate" id="editInterestRate" class="form-control"
                            step="0.01">
                    </div>

                    <!-- Installment Count -->
                    <div class="mb-3 col-md-6">
                        <label>{{ __('messages.installment_count') }}</label>
                        <input type="number" name="installment_count" id="editInstallmentCount" class="form-control"
                            required>
                    </div>

                    <!-- Start Date -->
                    <div class="mb-3 col-md-6">
                        <label>{{ __('messages.start_date') }}</label>
                        <input type="date" name="start_date" id="editStartDate" class="form-control" required>
                    </div>

                    <!-- Total Amount -->
                    <div class="mb-3 col-md-6">
                        <label>{{ __('messages.total_amount') }}</label>
                        <input type="number" name="total_amount" id="editTotalAmount" class="form-control"
                            step="0.01" readonly>
                    </div>

                    <!-- Remaining Amount -->
                    <div class="mb-3 col-md-6">
                        <label>{{ __('messages.remaining_amount') }}</label>
                        <input type="number" name="remaining_amount" id="editRemainingAmount" class="form-control"
                            step="0.01" readonly>
                    </div>

                    <!-- Remaining Installments -->
                    <div class="mb-3 col-md-6">
                        <label>{{ __('messages.installment_count_left') }}</label>
                        <input type="number" name="remaining_installments" id="editRemainingInstallments"
                            class="form-control" readonly>
                    </div>

                    <!-- Next Due Date -->
                    <div class="mb-3 col-md-6">
                        <label>{{ __('messages.next_installment_date') }}</label>
                        <input type="date" name="next_due_date" id="editNextDueDate" class="form-control" readonly>
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
