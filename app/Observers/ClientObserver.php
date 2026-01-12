<?php

namespace App\Observers;

use App\Models\Client;
use App\Models\ClientDebtLog;
use Illuminate\Support\Facades\Auth;

class ClientObserver
{

    public function updated(Client $client): void
    {
        if ($client->isDirty('debt')) {
            $debtBefore = $client->getOriginal('debt');
            $debtAfter = $client->debt;
            $changeAmount = $debtAfter - $debtBefore;

            $sourceType = null;
            $sourceId = null;
            $description = $client->log_description ?? null;

            if (isset($client->source_model)) {
                $sourceType = get_class($client->source_model);
                $sourceId = $client->source_model->id;
            }

            ClientDebtLog::create([
                'client_id' => $client->id,
                'debt_before' => $debtBefore,
                'debt_after' => $debtAfter,
                'change_amount' => $changeAmount,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'description' => $description,
                'created_by' => Auth::id(),
            ]);
        }
    }
}
