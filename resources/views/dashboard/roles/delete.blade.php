<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title fw-bold fs-5">{{ __('messages.delete_role') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('messages.delete_confirmation') }} <strong id="deleteName"></strong>؟</p>
                </div>
                <div class="modal-footer">
                    <button type="submit"
                        class="btn btn-outline-danger btn-sm radius-8">{{ __('messages.yes_delete') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm radius-8"
                        data-bs-dismiss="modal">{{ __('messages.no') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
