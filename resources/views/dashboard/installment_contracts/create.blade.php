<!-- Create Installment Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="createForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title fw-bold fs-5">{{ __('messages.create_installment') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row">

                    <!-- Client -->
                    <div class="mb-3 col-md-6">
                        <label>{{ __('messages.client') }}</label>
                        <select name="client_id" class="form-control" required>
                            <option value="">{{ __('messages.choose_client') }}</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }} - {{ $client->debt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Product -->
                    <div class="mb-3 col-md-6">
                        <label>{{ __('messages.product') }}</label>
                        <select name="product_id" class="form-control" id="productSelect" required>
                            <option value="">{{ __('messages.choose_product') }}</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Product Price -->
                    <div class="mb-3 col-md-6">
                        <label>{{ __('messages.product_price') }}</label>
                        <input type="number" name="product_price" id="productPrice" class="form-control" step="0.01" required>
                    </div>

                    <!-- Down Payment -->
                    <div class="mb-3 col-md-6">
                        <label>{{ __('messages.down_payment') }}</label>
                        <input type="number" name="down_payment" class="form-control" step="0.01" value="0">
                    </div>

                    <!-- Interest Rate -->
                    <div class="mb-3 col-md-6">
                        <label>{{ __('messages.interest_rate') }}</label>
                        <input type="number" name="interest_rate" class="form-control" step="0.01" value="0">
                    </div>

                    <!-- Installment Count -->
                    <div class="mb-3 col-md-6">
                        <label>{{ __('messages.installment_count') }}</label>
                        <input type="number" name="installment_count" class="form-control" required>
                    </div>

                    <!-- Start Date -->
                    <div class="mb-3 col-md-6">
                        <label>{{ __('messages.start_date') }}</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-primary btn-sm radius-8">{{ __('messages.save') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm radius-8" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
