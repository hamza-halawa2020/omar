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

                    <div class="">
                        <div class="position-relative mb-3">
                            <div class="icon-field">
                                <span class="icon top-50 translate-middle-y">
                                    <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                                </span>
                                <input type="password" name="password"
                                    class="form-control h-56-px bg-neutral-50 radius-12 pe-50" id="update-your-password"
                                    placeholder="{{ __('messages.password') }}">

                                <span class="update-toggle-password position-absolute top-50 end-0 translate-middle-y me-3"
                                    style="cursor: pointer;">
                                    <iconify-icon icon="mdi:eye-off-outline" class="show-icon"></iconify-icon>
                                    <iconify-icon icon="mdi:eye-outline" class="hide-icon"
                                        style="display: none;"></iconify-icon>
                                </span>
                            </div>
                        </div>
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.update-toggle-password').forEach(btn => {
            btn.addEventListener('click', () => {
                const passwordField = document.getElementById('update-your-password');
                const showIcon = btn.querySelector('.show-icon');
                const hideIcon = btn.querySelector('.hide-icon');

                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    showIcon.style.display = 'none';
                    hideIcon.style.display = 'inline';
                } else {
                    passwordField.type = 'password';
                    showIcon.style.display = 'inline';
                    hideIcon.style.display = 'none';
                }
            });
        });
    });
</script>
