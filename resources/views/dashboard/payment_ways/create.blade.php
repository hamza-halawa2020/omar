<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="createForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title fw-bold fs-5">{{ __('messages.create_payment_way') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>{{ __('messages.category') }}</label>
                        <select name="category_id" id="createCategorySelect" class="form-control" required>
                            <option value="">{{ __('messages.select_category') }}</option>
                            @foreach ($categories as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.sub_category') }}</label>
                        <select name="sub_category_id" id="createSubCategorySelect" class="form-control">
                            <option value="">{{ __('messages.select_sub_category') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.type') }}</label>
                        <select name="type" id="createType" class="form-control">
                            <option value="cash">{{ __('messages.cash') }}</option>
                            <option value="wallet">{{ __('messages.wallet') }}</option>
                            <option value="balance_machine">{{ __('messages.balance_machine') }}</option>
                        </select>
                    </div>
                    <div class="mb-3 phone_limit_group">
                        <label>{{ __('messages.phone_number') }}</label>
                        <input type="text" name="phone_number" class="form-control">
                    </div>
                    <div class="mb-3 phone_limit_group">
                        <label>{{ __('messages.send_limit') }}</label>
                        <input type="number" name="send_limit" class="form-control">
                    </div>
                    <div class="mb-3 phone_limit_group">
                        <label>{{ __('messages.receive_limit') }}</label>
                        <input type="number" name="receive_limit" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.balance') }}</label>
                        <input type="number" name="balance" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit"
                        class="btn btn-outline-primary btn-sm radius-8">{{ __('messages.save') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm radius-8"
                        data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
