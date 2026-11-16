<?php

return [
    'permissions' => [
        'umrah_access', 'umrah_trips_manage', 'umrah_bookings_manage',
        'umrah_payments_collect', 'umrah_payments_confirm', 'umrah_refunds_approve',
        'umrah_passports_view', 'umrah_procedures_manage', 'umrah_reports_view',
    ],
    'trip_statuses' => ['draft', 'open', 'closed', 'ongoing', 'completed', 'cancelled'],
    'visa_statuses' => ['not_started', 'processing', 'issued', 'rejected'],
    'ticket_statuses' => ['not_issued', 'issued', 'cancelled'],
    'travel_statuses' => ['not_traveled', 'departed', 'returned'],
];
