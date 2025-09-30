    {{-- Delete Modal --}}
    <div class="modal fade" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="deleteUserForm" method="POST" class="modal-content">
                @csrf
                @method('DELETE')
                <input type="hidden" name="id" id="deleteUserId">
                <div class="modal-header">
                    <div class="modal-title">{{ __('messages.delete_user') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('messages.delete_confirmation') }} <strong id="deleteUserName"></strong>؟</p>
                </div>
                <div class="modal-footer">
                    <button type="submit"
                        class="btn btn-outline-danger btn-sm radius-8">{{ __('messages.yes_delete') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm radius-8"
                        data-bs-dismiss="modal">{{ __('messages.no') }}</button>
                </div>
            </form>
        </div>
    </div>
