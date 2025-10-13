<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="createForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title fw-bold fs-5">{{ __('messages.create_association') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.per_day') }}</label>
                        <input type="text" name="per_day" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="clients">{{ __('messages.clients') }}</label>
                        <select cla id="clients" name="total_members[]" data-multi-select data-placeholder="{{ __('messages.select_clients') }}" multiple required>
                            @foreach ($clients as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>{{ __('messages.monthly_amount') }}</label>
                        <input type="number" step="0.01" name="monthly_amount" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>{{ __('messages.start_date') }}</label>
                        <input type="date" name="start_date" class="form-control" required>
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
