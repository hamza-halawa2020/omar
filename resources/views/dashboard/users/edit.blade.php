<!-- update User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <div class="modal-title" id="editUserModalLabel">{{ __('messages.edit_user') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="editUserForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editUserId" name="id">

                    <div class="mb-3">
                        <label>{{ __('messages.name') }}</label>
                        <input type="text" id="editUserName" name="name" class="form-control" required>
                        <span class="text-danger error-text name_error"></span>
                    </div>

                    <div class="mb-3">
                        <label>{{ __('messages.email') }}</label>
                        <input type="email" id="editUserEmail" name="email" class="form-control" required>
                        <span class="text-danger error-text email_error"></span>
                    </div>

                    <div class="mb-3">
                        <label>{{ __('messages.password') }}</label>
                        <input type="password" name="password" class="form-control">
                        <span class="text-danger error-text password_error"></span>
                    </div>

                    <div class="mb-3">
                        <label>{{ __('messages.roles') }}</label>
                        <select name="role" id="editUserRoles" class="form-select" required>
                            <option value="">{{ __('messages.select_role') }}</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger error-text role_error"></span>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm"
                    data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                <button type="button" id="updateUserBtn" class="btn btn-outline-primary btn-sm">
                    {{ __('messages.update') }}
                </button>
            </div>
        </div>
    </div>
</div>
