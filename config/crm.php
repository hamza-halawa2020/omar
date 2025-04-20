<?php
return [
    'table_prefix' => 'crm_',

    'lead_reminders' => [
        'max_reminders' => 2 * 60,  //  convert to hours
        'interval_hours' => 3 * 30, //  convert to hours
    ],
];
