@extends('dashboard.umrah.layout')
@section('umrah_title', $traveler->name)
@section('umrah_back', route('umrah.travelers.index'))
@section('umrah')
<nav class="umrah-section-nav no-print" aria-label="{{ __('umrah.section_navigation') }}"><a href="#umrah-traveler-summary">{{ __('umrah.overview') }}</a><a href="#umrah-traveler-bookings">{{ __('umrah.bookings') }}</a>@can('umrah_passports_view')<a href="#umrah-traveler-passport">{{ __('umrah.passport_movements') }}</a><a href="#umrah-traveler-documents">{{ __('umrah.private_documents') }}</a>@endcan<a href="#umrah-traveler-messages">{{ __('umrah.whatsapp_log') }}</a></nav>
    <div class="card" id="umrah-traveler-summary">
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
    <div class="card" id="umrah-traveler-bookings">
        <div class="card-body">
            <div class="umrah-section-title">{{ __('umrah.traveler_bookings') }}</div>
            <ul>@forelse($traveler->bookings as $booking)
                <li class="mb-2"><a href="{{ route('umrah.bookings.show', $booking->id) }}">{{ $booking->trip->name }} —
            {{ $booking->reference }}</a> — {{ __('umrah.' . $booking->status) }}</li>@empty<li>
                        {{ __('umrah.no_traveler_bookings') }}</li>@endforelse
            </ul>
        </div>
    </div>
    @can('umrah_passports_view')
        <div class="card" id="umrah-traveler-passport">
            <div class="card-body">
                <div class="umrah-section-title">{{ __('umrah.passport_movements') }}</div>
                <div class="table-responsive umrah-records-wrapper">
                    <table class="table umrah-records" role="table">
                        <thead role="rowgroup">
                            <tr role="row">
                                <th role="columnheader" scope="col">{{ __('umrah.date') }}</th>
                                <th role="columnheader" scope="col">{{ __('umrah.passport_location') }}</th>
                                <th role="columnheader" scope="col">{{ __('umrah.recipient') }}</th>
                                <th role="columnheader" scope="col">{{ __('umrah.employee') }}</th>
                                <th role="columnheader" scope="col">{{ __('umrah.notes') }}</th>
                            </tr>
                        </thead>
                        <tbody role="rowgroup">
                            @forelse($traveler->movements->sortByDesc('id') as $movement)
                                <tr role="row">
                                    <td role="cell" data-label="{{ __('umrah.date') }}" data-field="date"><div class="umrah-cell-value">{{ $movement->created_at->timezone('Africa/Cairo')->format('Y-m-d H:i') }}</div></td>
                                    <td role="cell" data-label="{{ __('umrah.passport_location') }}" data-field="passport_location"><div class="umrah-cell-value">{{ $movement->location }}</div></td>
                                    <td role="cell" data-label="{{ __('umrah.recipient') }}" data-field="recipient"><div class="umrah-cell-value">{{ $movement->recipient }}</div></td>
                                    <td role="cell" data-label="{{ __('umrah.employee') }}" data-field="employee"><div class="umrah-cell-value">#{{ $movement->created_by }}</div></td>
                                    <td role="cell" data-label="{{ __('umrah.notes') }}" data-field="notes"><div class="umrah-cell-value">{{ $movement->notes }}</div></td>
                            </tr>@empty<tr role="row">
                                <td role="cell" colspan="5">{{ __('umrah.no_movements') }}</td>
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
        <div class="card" id="umrah-traveler-documents">
            <div class="card-body">
                <div class="umrah-section-title">{{ __('umrah.private_documents') }}</div>
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
    <div class="card" id="umrah-traveler-messages">
        <div class="card-body">
            <div class="umrah-section-title">{{ __('umrah.whatsapp_log') }}</div>
            <p class="text-muted">{{ __('umrah.whatsapp_delivery_help') }}</p>
            <div class="table-responsive umrah-records-wrapper">
                <table class="table umrah-records" role="table">
                    <thead role="rowgroup">
                        <tr role="row">
                            <th role="columnheader" scope="col">{{ __('umrah.date') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.event_message') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.status') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.attempts') }}</th>
                            <th role="columnheader" scope="col">{{ __('umrah.result') }}</th>
                            <th role="columnheader" scope="col"></th>
                        </tr>
                    </thead>
                    <tbody role="rowgroup">
                        @forelse($notifications as $notification)
                            <tr role="row">
                                <td role="cell" data-label="{{ __('umrah.date') }}" data-field="date"><div class="umrah-cell-value">{{ $notification->created_at->timezone('Africa/Cairo')->format('Y-m-d H:i') }}</div></td>
                                <td role="cell" data-label="{{ __('umrah.event_message') }}" data-field="event_message"><div class="umrah-cell-value">
                                    <details>
                                        <summary>{{ __('umrah.' . $notification->event) }}</summary>
                                        <div style="white-space:pre-wrap;max-width:440px">{{ $notification->message }}</div>
                                    </details>
                                </div></td>
                                <td role="cell" data-label="{{ __('umrah.status') }}" data-field="status"><div class="umrah-cell-value">{{ __('umrah.notification_' . $notification->status) }}</div></td>
                                <td role="cell" data-label="{{ __('umrah.attempts') }}" data-field="attempts"><div class="umrah-cell-value">{{ $notification->attempts }}</div></td>
                                <td role="cell" data-label="{{ __('umrah.result') }}" data-field="result"><div class="umrah-cell-value">{{ $notification->result_code }}</div></td>
                                <td role="cell" data-label="{{ __('umrah.actions') }}" data-field="actions"><div class="umrah-cell-value">
                                    @can('umrah_procedures_manage')@if(in_array($notification->status, ['failed', 'skipped']) && $traveler->whatsapp_enabled)
                                        <form method="POST" action="{{ route('umrah.notifications.retry', $notification->id) }}">
                                            @csrf<button class="btn btn-sm btn-outline-primary">{{ __('umrah.retry') }}</button>
                                    </form>@endif@endcan
                                </div></td>
                        </tr>@empty<tr role="row">
                            <td role="cell" colspan="6">{{ __('umrah.no_messages') }}</td>
                        </tr>@endforelse
                    </tbody>
                </table>
            </div>{{ $notifications->links('dashboard.umrah.pagination') }}
        </div>
    </div>
@endsection