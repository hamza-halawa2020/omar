<?php

namespace App\Enums\Calls;

use App\Models\Contact;
use App\Models\Deal;
use App\Models\Lead;

enum RelatedToType: string
{
    case LEAD = Lead::class;
    case CONTACT = Contact::class;
    case DEAL = Deal::class;
}
