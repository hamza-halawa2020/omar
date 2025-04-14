<?php

namespace App\Enums\Leads;

enum SourceType: string
{
    case WEBSITE = 'website';
    case REFERRAL = 'referral';
    case ADS = 'ads';
}