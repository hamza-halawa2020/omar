<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editId">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title fw-bold fs-5">{{ __('messages.edit_client') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>{{ __('messages.name') }}</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.phone_number') }}</label>
                        <div class="row g-2">
                            <div class="col-4">
                                <select name="country_code" id="editCountryCode" class="form-control country-code-select" required>
                                    @foreach ($countryCodeOptions as $countryCode)
                                        <option value="{{ $countryCode['code'] }}">{{ $countryCode['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-8">
                                <input type="text" name="phone_number" id="editPhoneNumber" class="form-control" required>
                            </div>
                        </div>
                    </div>
                     <div class="mb-3">
                        <label>{{ __('messages.type') }}</label>
                        <select name="type"  id="editTypeId" class="form-control">
                            <option value="client">{{ __('messages.client') }}</option>
                            <option value="merchant">{{ __('messages.merchant') }}</option>

                        </select>
                    </div>
                    <div class="mb-3">
                        <label>{{ __('messages.debt') }}</label>
                        <input type="text" name="debt" id="editDebt" class="form-control" required>
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

@once
    @push('scripts')
        <script>
            $(document).ready(function () {
                function initializeCountryCodeSelect($select, $modal) {
                    if (!$.fn.select2 || !$select.length) {
                        return;
                    }

                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }

                    $select.select2({
                        width: '100%',
                        dropdownParent: $modal,
                        dir: $('html').attr('dir') || 'rtl'
                    });
                }

                $('#createModal').on('shown.bs.modal', function () {
                    initializeCountryCodeSelect($('#createCountryCode'), $('#createModal'));
                });

                $('#editModal').on('shown.bs.modal', function () {
                    initializeCountryCodeSelect($('#editCountryCode'), $('#editModal'));
                });
            });
        </script>
    @endpush
@endonce
