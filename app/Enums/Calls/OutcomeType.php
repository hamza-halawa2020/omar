<?php

namespace App\Enums\Calls;

enum OutcomeType: string
{
    case COMPLETED = 'completed';
    case NO_ANSWER = 'no_answer';
    case RESCHEDULED = 'rescheduled';
}
