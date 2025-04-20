<?php

namespace Database\Seeders;

use App\Enums\Leads\StatusType;
use App\Models\LeadsStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadsStatusSeeder extends Seeder
{
    protected bool $defaultSet = false; // Track if we've already set the default

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('crm_leads_statuses')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = [
            StatusType::NOT_CONTACTED->value => [
                StatusType::ANSWER->value => [
                    StatusType::INTERESTED->value => [
                        StatusType::RESCHEDULE->value => [
                            StatusType::FOLLOW_UP_PAYMENT->value,
                            StatusType::TEST->value,
                            StatusType::DEMO->value,
                            StatusType::PAID->value,
                            StatusType::NEGOTIATION->value,
                            StatusType::NO_ANSWER->value,
                            StatusType::CLOSE_LOST->value,
                        ],
                        StatusType::FOLLOW_UP_PAYMENT->value => [
                            StatusType::FOLLOW_UP_PAYMENT->value,
                            StatusType::FOLLOW_UP_PAYMENT->value,
                            StatusType::FOLLOW_UP_PAYMENT->value,
                            StatusType::FOLLOW_UP_PAYMENT->value
                        ],
                        StatusType::TEST->value => [
                            StatusType::TEST->value,
                            StatusType::TEST->value,
                            StatusType::TEST->value,
                            StatusType::TEST->value,
                            StatusType::TEST->value,
                            StatusType::TEST->value,
                        ],
                        StatusType::DEMO->value => [
                            StatusType::DEMO->value,
                            StatusType::DEMO->value,
                            StatusType::DEMO->value,
                            StatusType::DEMO->value,
                            StatusType::DEMO->value,
                            StatusType::DEMO->value,
                        ],
                        StatusType::PAID->value => [
                            StatusType::PAID->value,
                        ],
                        StatusType::NEGOTIATION->value => [
                            StatusType::FOLLOW_UP_PAYMENT->value,
                            StatusType::TEST->value,
                            StatusType::DEMO->value,
                            StatusType::PAID->value,
                            StatusType::NEGOTIATION->value,
                        ],
                    ],
                    StatusType::NOT_INTERESTED->value,
                ],
                StatusType::NO_ANSWER->value => [
                    StatusType::ANSWER->value,
                    StatusType::NO_ANSWER->value,
                    StatusType::WRONG_NUMBER->value,
                    StatusType::SWITCHED_OFF->value,
                    StatusType::INVALID_NUMBER->value,
                ],
                StatusType::WRONG_NUMBER->value,
                StatusType::SWITCHED_OFF->value,
                StatusType::INVALID_NUMBER->value,
            ]
        ];

        DB::transaction(function () use ($data) {
            $this->createStatuses($data);
        });
    }

    protected function createStatuses(array $nodes, ?int $parentId = null): void
    {
        foreach ($nodes as $key => $value) {
            if (is_array($value)) {
                // Create the parent status
                $parent = LeadsStatus::create([
                    'name' => $key,
                    'parent_id' => $parentId,
                    'is_default' => !$this->defaultSet,
                ]);

                // Only set default once
                $this->defaultSet = true;

                // Recurse into children
                $this->createStatuses($value, $parent->id);
            } else {
                // Leaf node
                LeadsStatus::create([
                    'name' => $value,
                    'parent_id' => $parentId,
                    'is_default' => false,
                ]);
            }
        }
    }
}
