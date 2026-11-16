<?php

namespace App\Models\Umrah;

use Illuminate\Database\Eloquent\Model;

class PaymentSchedule extends Model
{
    protected $table = 'umrah_payment_schedules';

    protected $guarded = ['id'];

    protected $casts = ['amount' => 'decimal:2', 'due_date' => 'date'];
}
