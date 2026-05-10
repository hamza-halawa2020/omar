<?php

namespace App\Services;

use App\Models\TransactionLog;
use Illuminate\Database\Eloquent\Collection;

class TransactionLogService
{
    public function list(): Collection
    {
        return TransactionLog::with(['transaction', 'creator'])->latest()->get();
    }
}
