<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">{{ __('messages.create_user') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="createUserForm">
                    @csrf
                    <div class="mb-3">
                        <label>{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control" required>
                        <span class="text-danger error-text name_error"></span>
                    </div>

                    <div class="mb-3">
                        <label>{{ __('messages.email') }}</label>
                        <input type="email" name="email" class="form-control" required>
                        <span class="text-danger error-text email_error"></span>
                    </div>

                    <div class="mb-3">
                        <label>{{ __('messages.password') }}</label>
                        <input type="password" name="password" class="form-control" required>
                        <span class="text-danger error-text password_error"></span>
                    </div>

                    <div class="mb-3">
                        <label>{{ __('messages.role') }}</label>
                        <select name="role" class="form-select" required>
                            <option value="">{{ __('messages.select') }}</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger error-text role_error"></span>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                <button type="button" id="saveUserBtn" class="btn btn-success">{{ __('messages.save') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Alert place -->
<div id="alertMsg" class="mt-3"></div>

