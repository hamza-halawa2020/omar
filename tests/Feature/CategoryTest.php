<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function testIndex(): void
    // {
    //     $category = Category::get();
    //     $response = $this->get(route('categories.index'));
    //     $response->assertStatus(200);
    // }


    public function testIndex(): void
    {
        $tenant = Tenant::all()->first(function (Tenant $tenant) {
            return $this->tenantDatabaseExists($tenant);
        });

        if (! $tenant) {
            $this->markTestSkipped('No provisioned tenant database is available for this feature test.');
        }

        $user = User::where('tenant_id', $tenant->id)->first();

        if (! $user) {
            $this->markTestSkipped('No central user exists for the provisioned tenant database.');
        }

        $response = $this
            ->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('categories.index'));

        $response->assertStatus(200);
    }

    private function tenantDatabaseExists(Tenant $tenant): bool
    {
        $database = $tenant->database()->getName();

        try {
            $pdo = DB::connection('central')->getPdo();
            $statement = $pdo->query('SHOW DATABASES LIKE ' . $pdo->quote($database));

            return (bool) $statement->fetchColumn();
        } catch (\Throwable) {
            return false;
        }
    }
}
