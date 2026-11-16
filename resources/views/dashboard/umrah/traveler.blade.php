@extends('dashboard.umrah.layout')
@section('umrah')
    <div class="card">
        <div class="card-body">
            <div>{{ $traveler->name }}</div>
            <p>{{ $traveler->english_name }} — <bdi>{{ $traveler->phone }}</bdi></p>
            <p>{{ __('umrah.whatsapp_notifications') }}:
                {{ $traveler->whatsapp_enabled ? __('umrah.enabled') : __('umrah.disabled') }}</p>
            @can('umrah_passports_view')
                <p>{{ __('umrah.passport_number') }}: {{ $traveler->passport_number ?? __('umrah.not_recorded') }} —
                    {{ __('umrah.expires') }}:
                    {{ $traveler->passport_expires_at?->format('Y-m-d') ?? __('umrah.not_recorded') }} —
                    {{ __('umrah.location') }}: {{ $traveler->passport_location ?? __('umrah.not_recorded') }}</p>
                @can('umrah_bookings_manage')
                    <details>
                        <summary>{{ __('umrah.edit_traveler') }}</summary>
                        <form method="POST" action="{{ route('umrah.travelers.update', $traveler->id) }}">@csrf @method('PUT')
                            @include('dashboard.umrah.traveler-fields', ['formKey' => 'traveler'])<button
                                class="btn btn-primary">{{ __('umrah.save_details') }}</button>
                        </form>
                </details>@endcan
            @endcan
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div>{{ __('umrah.traveler_bookings') }}</div>
            <ul>@forelse($traveler->bookings as $booking)
                <li class="mb-2"><a href="{{ route('umrah.bookings.show', $booking->id) }}">{{ $booking->trip->name }} —
            {{ $booking->reference }}</a> — {{ __('umrah.' . $booking->status) }}</li>@empty<li>
                        {{ __('umrah.no_traveler_bookings') }}</li>@endforelse
            </ul>
        </div>
    </div>
    @can('umrah_passports_view')
        <div class="card">
            <div class="card-body">
                <div>{{ __('umrah.passport_movements') }}</div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('umrah.date') }}</th>
                                <th>{{ __('umrah.passport_location') }}</th>
                                <th>{{ __('umrah.recipient') }}</th>
                                <th>{{ __('umrah.employee') }}</th>
                                <th>{{ __('umrah.notes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($traveler->movements->sortByDesc('id') as $movement)
                                <tr>
                                    <td>{{ $movement->created_at->timezone('Africa/Cairo')->format('Y-m-d H:i') }}</td>
                                    <td>{{ $movement->location }}</td>
                                    <td>{{ $movement->recipient }}</td>
                                    <td>#{{ $movement->created_by }}</td>
                                    <td>{{ $movement->notes }}</td>
                            </tr>@empty<tr>
                                <td colspan="5">{{ __('umrah.no_movements') }}</td>
                            </tr>@endforelse
                        </tbody>
                    </table>
                </div><button class="btn btn-outline-secondary no-print"
                    onclick="window.print()">{{ __('umrah.print_movements') }}</button>
                @can('umrah_procedures_manage')
                    <details>
                        <summary>{{ __('umrah.record_handover') }}</summary>
                        <form method="POST" action="{{ route('umrah.movements.store', $traveler->id) }}">@csrf
                            @include('dashboard.umrah.fields', ['formKey' => 'passport', 'fields' => [['location', __('umrah.location_hint'), 'text', '', true], ['recipient', __('umrah.recipient_name'), 'text', '', true], ['notes', __('umrah.internal_notes'), 'textarea', '']]])
                            <button class="btn btn-primary">{{ __('umrah.record_movement') }}</button>
                        </form>
                </details>@endcan
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div>{{ __('umrah.private_documents') }}</div>
                <ul>@forelse($traveler->documents as $document)
                    <li class="mb-2"><a href="{{ route('umrah.documents.download', $document->id) }}">{{ $document->label }}</a>
                — {{ $document->created_at->format('Y-m-d') }}</li>@empty<li>{{ __('umrah.no_documents') }}</li>
                        @endforelse
                </ul>
                @can('umrah_procedures_manage')
                    <form method="POST" enctype="multipart/form-data" action="{{ route('umrah.documents.store', $traveler->id) }}">
                        @csrf
                        @include('dashboard.umrah.fields', ['formKey' => 'document', 'fields' => [['label', __('umrah.document_description'), 'text', '', true]]])
                        <label for="document-file">{{ __('umrah.document_upload_hint') }}</label><input id="document-file"
                            type="file" name="document" accept=".pdf,.png,.jpg,.jpeg" class="form-control mb-3" required><button
                            class="btn btn-primary">{{ __('umrah.upload_document') }}</button>
                </form>@endcan
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-body">
            <div>{{ __('umrah.whatsapp_log') }}</div>
            <p class="text-muted">{{ __('umrah.whatsapp_delivery_help') }}</p>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('umrah.date') }}</th>
                            <th>{{ __('umrah.event_message') }}</th>
                            <th>{{ __('umrah.status') }}</th>
                            <th>{{ __('umrah.attempts') }}</th>
                            <th>{{ __('umrah.result') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notification)
                            <tr>
                                <td>{{ $notification->created_at->timezone('Africa/Cairo')->format('Y-m-d H:i') }}</td>
                                <td>
                                    <details>
                                        <summary>{{ __('umrah.' . $notification->event) }}</summary>
                                        <div style="white-space:pre-wrap;max-width:440px">{{ $notification->message }}</div>
                                    </details>
                                </td>
                                <td>{{ __('umrah.notification_' . $notification->status) }}</td>
                                <td>{{ $notification->attempts }}</td>
                                <td>{{ $notification->result_code }}</td>
                                <td>
                                    @can('umrah_procedures_manage')@if(in_array($notification->status, ['failed', 'skipped']) && $traveler->whatsapp_enabled)
                                        <form method="POST" action="{{ route('umrah.notifications.retry', $notification->id) }}">
                                            @csrf<button class="btn btn-sm btn-outline-primary">{{ __('umrah.retry') }}</button>
                                    </form>@endif@endcan
                                </td>
                        </tr>@empty<tr>
                            <td colspan="6">{{ __('umrah.no_messages') }}</td>
                        </tr>@endforelse
                    </tbody>
                </table>
            </div>{{ $notifications->links() }}
        </div>
    </div>
@endsection