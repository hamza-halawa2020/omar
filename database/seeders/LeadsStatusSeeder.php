<?php

namespace Database\Seeders;

use App\Enums\Leads\StatusType;
use App\Models\LeadsStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadsStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            StatusType::NOT_CONTACTED->value => [
                StatusType::ANSWER->value,
                StatusType::NO_ANSWER->value,
                StatusType::WRONG_NUMBER->value,
                StatusType::SWITCHED_OFF->value,
                StatusType::INVALID_NUMBER->value,
            ]
        ];

        // Recursively create data inside this array
        DB::transaction(function () use ($data) {
            $this->createStatuses($data);
        });
    }

    function createStatuses(array $nodes, ?int $parentId = null): void
    {
        foreach ($nodes as $key => $value) {
            if (is_array($value)) {
                // If the key is a string, treat it as the parent name
                $parent = LeadsStatus::create([
                    'name' => $key,
                    'parent_id' => $parentId,
                    'is_default' => true,   // make the very first one the default
                ]);

                // Recursively create children
                $this->createStatuses($value, $parent->id);
            } else {
                // Value is a leaf node (string)
                LeadsStatus::create([
                    'name' => $value,
                    'parent_id' => $parentId,
                ]);
            }
        }
    }
}
