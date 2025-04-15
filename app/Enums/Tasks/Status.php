<?php

namespace App\Enums\Tasks;

enum Status: string
{
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case PENDING = 'pending';
}
