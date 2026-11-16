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
                <div class="table-responsive responsive-records-wrapper">
                    <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('umrah.date') }}</th>
                                <th class="text-center">{{ __('umrah.passport_location') }}</th>
                                <th class="text-center">{{ __('umrah.recipient') }}</th>
                                <th class="text-center">{{ __('umrah.employee') }}</th>
                                <th class="text-center">{{ __('umrah.notes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($traveler->movements->sortByDesc('id') as $movement)
                                <tr>
                                    <td class="mobile-primary" data-label="{{ __('umrah.date') }}">{{ $movement->created_at->timezone('Africa/Cairo')->format('Y-m-d H:i') }}</td>
                                    <td data-label="{{ __('umrah.passport_location') }}">{{ $movement->location }}</td>
                                    <td data-label="{{ __('umrah.recipient') }}">{{ $movement->recipient }}</td>
                                    <td class="mobile-muted" data-label="{{ __('umrah.employee') }}">#{{ $movement->created_by }}</td>
                                    <td class="mobile-muted" data-label="{{ __('umrah.notes') }}">{{ $movement->notes }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">{{ __('umrah.no_movements') }}</td>
                                </tr>
                            @endforelse
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
            <p class="">{{ __('umrah.whatsapp_delivery_help') }}</p>
            <div class="table-responsive responsive-records-wrapper">
                <table class="text-center table table-bordered table-sm table bordered-table sm-table mb-0 responsive-records">
                    <thead>
                        <tr>
                            <th class="text-center">{{ __('umrah.date') }}</th>
                            <th class="text-center">{{ __('umrah.event_message') }}</th>
                            <th class="text-center">{{ __('umrah.status') }}</th>
                            <th class="text-center">{{ __('umrah.attempts') }}</th>
                            <th class="text-center">{{ __('umrah.result') }}</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notification)
                            <tr>
                                <td class="mobile-primary" data-label="{{ __('umrah.date') }}">{{ $notification->created_at->timezone('Africa/Cairo')->format('Y-m-d H:i') }}</td>
                                <td data-label="{{ __('umrah.event_message') }}">
                                    <details>
                                        <summary>{{ __('umrah.' . $notification->event) }}</summary>
                                        <div style="white-space:pre-wrap;max-width:440px">{{ $notification->message }}</div>
                                    </details>
                                </td>
                                <td data-label="{{ __('umrah.status') }}">{{ __('umrah.notification_' . $notification->status) }}</td>
                                <td class="mobile-muted" data-label="{{ __('umrah.attempts') }}">{{ $notification->attempts }}</td>
                                <td class="mobile-muted" data-label="{{ __('umrah.result') }}">{{ $notification->result_code }}</td>
                                <td class="mobile-actions" data-label="{{ __('umrah.actions') }}">
                                    @can('umrah_procedures_manage') @if(in_array($notification->status, ['failed', 'skipped']) && $traveler->whatsapp_enabled)
                                        <form method="POST" action="{{ route('umrah.notifications.retry', $notification->id) }}">
                                            @csrf<button class="btn btn-sm btn-outline-primary">{{ __('umrah.retry') }}</button>
                                        </form>
                                    @endif @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">{{ __('umrah.no_messages') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-table-pagination id="notificationsPagination" :paginator="$notifications" />
        </div>
    </div>
@endsection