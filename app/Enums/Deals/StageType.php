<?php

namespace App\Enums\Deals;

enum StageType: string
{
    case NEW = 'new';
    case QUALIFIED = 'qualified';
    case PROPOSAL = 'proposal';
    case NEGOTIATION = 'negotiation';
    case WON = 'won';
    case LOST = 'lost';
}
