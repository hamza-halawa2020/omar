<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="createForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title fw-bold fs-5">{{ __('messages.create_client') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.phone_number') }}</label>
                        <div class="row g-2">
                            <div class="col-4">
                                <select name="country_code" class="form-control" required>
                                    @foreach ($countryCodeOptions as $countryCode)
                                        <option value="{{ $countryCode['code'] }}" @selected($countryCode['code'] === '+20')>
                                            {{ $countryCode['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-8">
                                <input type="text" name="phone_number" class="form-control" required>
                            </div>
                        </div>
                    </div>

                      <div class="mb-3">
                        <label>{{ __('messages.type') }}</label>
                        <select name="type"  class="form-control">
                            <option value="client">{{ __('messages.client') }}</option>
                            <option value="merchant">{{ __('messages.merchant') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>{{ __('messages.debt') }}</label>
                        <input type="text" name="debt" class="form-control" required>
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
