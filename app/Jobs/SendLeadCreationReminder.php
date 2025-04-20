<?php

namespace App\Jobs;

use App\Enums\Leads\StatusType;
use App\Models\Lead;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendLeadCreationReminder implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly int $leadId, private readonly int $reminderNumber = 1)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $lead = Lead::find($this->leadId);

        if (!$lead || $lead->status->name !== StatusType::NOT_CONTACTED->value)
            return;

        // Set the reminder here
        Log::info("Sending reminder #$this->reminderNumber to Lead ID $lead->id");

        $maxReminders = config('crm.lead_reminders.max_reminders');
        $intervalHours = config('crm.lead_reminders.interval_hours');

        if ($this->reminderNumber < $maxReminders)
            dispatch(new SendLeadCreationReminder($this->leadId, $this->reminderNumber + 1))
                ->delay(now()->addMinutes($intervalHours));
        else
            dispatch(new CreateCallTaskForLead($this->leadId))
                ->delay(now()->addMinutes($intervalHours));
    }
}
