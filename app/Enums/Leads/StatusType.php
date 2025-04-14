<?php

namespace App\Enums\Leads;

enum StatusType: string
{
    case WIN = 'win';
    case LOSE = 'lose';
    case NEW_TASK = 'new_task';
    case NO_ANSWER = 'no_answer';
}