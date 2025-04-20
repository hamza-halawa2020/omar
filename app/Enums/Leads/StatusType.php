<?php

namespace App\Enums\Leads;

enum StatusType: string
{
    case NOT_CONTACTED = 'not_contacted';   // The default status
    case ANSWER = 'answer';
    case NO_ANSWER = 'no_answer';
    case WRONG_NUMBER = 'wrong_number';
    case SWITCHED_OFF = 'switched_off';
    case INVALID_NUMBER = 'invalid_number';

    case INTERESTED = 'interested';
    case NOT_INTERESTED = 'not_interested';
    case RESCHEDULE = 'reschedule';
    case FOLLOW_UP_PAYMENT = 'follow_up_payment';
    case TEST = 'test';
    case DEMO = 'demo';
    case PAID = 'paid';
    case NEGOTIATION = 'negotiation';
    case CLOSE_LOST = 'close_lost';
}
