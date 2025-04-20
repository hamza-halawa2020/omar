<?php

namespace App\Jobs;

use App\Enums\Leads\StatusType;
use App\Enums\Tasks\RelatedToType;
use App\Enums\Tasks\Status;
use App\Models\Lead;
use App\Services\TaskServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CreateCallTaskForLead implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly int $leadId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(TaskServiceInterface $taskService): void
    {
        $lead = Lead::find($this->leadId);

        if (!$lead || $lead->status->name !== StatusType::NOT_CONTACTED->value)
            return;

        $taskService->create([
            'title' => "Call the lead $lead->name",
            'due_date' => now()->addDay(),
            'status' => Status::PENDING->value,
            'related_to_type' => RelatedToType::LEAD->value,
            'related_to_id' => $lead->id,
            'assigned_to' => 1, // AUTH ISSUE
        ]);

        Log::info("Created call task for Lead ID {$lead->id}");
    }
}
