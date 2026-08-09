@extends('dashboard.layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3 mobile-stack-header">
        <div>Tenants</div>
        <button class="btn btn-outline-primary btn-sm radius-8" data-bs-toggle="modal" data-bs-target="#createTenantModal">
            Add Tenant
        </button>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped mb-0 responsive-records">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Domain</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tenants as $tenant)
                    <tr>
                        <td data-label="#"> {{ $loop->iteration }}</td>
                        <td class="mobile-primary" data-label="Name">{{ $tenant->name }}</td>
                        <td data-label="Domain">{{ $tenant->domain }}</td>
                        <td data-label="Created">{{ $tenant->created_at?->format('Y-m-d') }}</td>
                        <td class="mobile-actions" data-label="Actions">
                            <button class="btn btn-sm btn-outline-danger"
                                onclick="deleteTenant('{{ $tenant->id }}')">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center" data-label="No tenants found">No tenants found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createTenantModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="createTenantForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Tenant</h5></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Domain</label>
                        <input type="text" name="domain" class="form-control" placeholder="company.com" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('createTenantForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const data = new FormData(this);
    fetch('{{ route('tenants.store') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: data
    }).then(r => r.json()).then(res => {
        if (res.status) location.reload();
        else alert(res.message);
    });
});

function deleteTenant(id) {
    if (!confirm('Delete this tenant and all its data?')) return;
    fetch(`/dashboard/tenants/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    }).then(r => r.json()).then(res => {
        if (res.status) location.reload();
    });
}
</script>
@endpush
@endsection
