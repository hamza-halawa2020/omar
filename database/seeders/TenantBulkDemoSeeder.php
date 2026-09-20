<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TenantBulkDemoSeeder extends Seeder
{
    private int $targetCount;

    public function run(): void
    {
        $this->targetCount = max((int) env('BULK_DEMO_TARGET', 1001), 1001);

        Tenant::query()->each(function (Tenant $tenant) {
            $this->seedCentralUsers($tenant);

            $tenant->run(function () use ($tenant) {
                $this->call([
                    PermissionSeeder::class,
                    RolesSeeder::class,
                ]);

                $this->seedTenantTables($tenant);
            });
        });
    }

    private function seedCentralUsers(Tenant $tenant): void
    {
        $existing = User::on('central')->where('tenant_id', $tenant->id)->count();
        $missing = $this->targetCount - $existing;

        if ($missing <= 0) {
            return;
        }

        $now = now();
        $rows = [];

        for ($i = 1; $i <= $missing; $i++) {
            $number = $existing + $i;
            $rows[] = [
                'name' => "Bulk User {$number}",
                'email' => "bulk-user-{$tenant->id}-{$number}@example.test",
                'password' => Hash::make('12345678'),
                'tenant_id' => $tenant->id,
                'is_active' => $number % 7 !== 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $this->insertChunks('central.users', $rows, 'central');
    }

    private function seedTenantTables(Tenant $tenant): void
    {
        $faker = FakerFactory::create();
        $userIds = User::on('central')->where('tenant_id', $tenant->id)->pluck('id')->all();

        if (empty($userIds)) {
            return;
        }

        $this->seedPermissions();
        $this->seedRoles();
        $this->seedRolePermissions();
        $this->seedModelRoles($userIds);
        $this->seedModelPermissions($userIds);

        $this->topUp('categories', fn (int $number) => [
            'name' => "Bulk Category {$number}",
            'parent_id' => $number > 20 ? $this->randomId('categories') : null,
            'created_by' => $this->randomValue($userIds),
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ]);

        $this->topUp('clients', fn (int $number) => [
            'name' => "Bulk Client {$number}",
            'type' => $number % 5 === 0 ? 'merchant' : 'client',
            'phone_number' => '01' . str_pad((string) $number, 9, '0', STR_PAD_LEFT),
            'country_code' => '+20',
            'debt' => $faker->randomFloat(2, 0, 25000),
            'created_by' => $this->randomValue($userIds),
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ]);

        $this->topUp('products', fn (int $number) => [
            'name' => "Bulk Product {$number}",
            'code' => 'PRD-' . str_pad((string) $number, 6, '0', STR_PAD_LEFT),
            'image' => null,
            'description' => "Demo product {$number}",
            'purchase_price' => $faker->randomFloat(2, 50, 10000),
            'sale_price' => $faker->randomFloat(2, 100, 15000),
            'stock' => $faker->numberBetween(10, 500),
            'created_by' => $this->randomValue($userIds),
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ]);

        $this->topUp('payment_ways', fn (int $number) => [
            'name' => "Bulk Payment Way {$number}",
            'type' => ['cash', 'wallet', 'balance_machine'][$number % 3],
            'phone_number' => $number % 3 === 1 ? '010' . str_pad((string) $number, 8, '0', STR_PAD_LEFT) : null,
            'send_limit' => 50000,
            'send_limit_alert' => 40000,
            'receive_limit' => 80000,
            'receive_limit_alert' => 65000,
            'balance' => $faker->randomFloat(2, 1000, 100000),
            'position' => $number,
            'client_type' => $number % 5 === 0 ? 'merchant' : 'client',
            'created_by' => $this->randomValue($userIds),
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ]);

        $clientIds = $this->ids('clients');
        $productIds = $this->ids('products');
        $paymentWayIds = $this->ids('payment_ways');

        $this->topUp('payment_way_limits', fn (int $number) => [
            'payment_way_id' => $this->randomValue($paymentWayIds),
            'month' => ($number % 12) + 1,
            'year' => now()->year,
            'send_limit' => 50000,
            'send_used' => $faker->randomFloat(2, 0, 25000),
            'receive_limit' => 80000,
            'receive_used' => $faker->randomFloat(2, 0, 40000),
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ]);

        $this->topUp('transactions', fn (int $number) => $this->transactionRow(
            $number,
            $paymentWayIds,
            $clientIds,
            $productIds,
            $userIds
        ));

        $transactionIds = $this->ids('transactions');

        $this->topUp('transaction_payments', fn (int $number) => [
            'transaction_id' => $this->randomValue($transactionIds),
            'payment_way_id' => $this->randomValue($paymentWayIds),
            'amount' => $faker->randomFloat(2, 50, 20000),
            'balance_before_transaction' => $faker->randomFloat(2, 1000, 50000),
            'balance_after_transaction' => $faker->randomFloat(2, 1000, 50000),
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ]);

        $this->topUp('transaction_logs', fn (int $number) => [
            'transaction_id' => $this->randomValue($transactionIds),
            'created_by' => $this->randomValue($userIds),
            'action' => ['create', 'update', 'delete'][$number % 3],
            'data' => json_encode(['seeded' => true, 'number' => $number]),
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ]);

        $this->topUp('payment_way_logs', fn (int $number) => [
            'payment_way_id' => $this->randomValue($paymentWayIds),
            'created_by' => $this->randomValue($userIds),
            'action' => ['create', 'update', 'delete'][$number % 3],
            'data' => json_encode(['seeded' => true, 'number' => $number]),
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ]);

        $this->topUp('transaction_products', fn (int $number) => $this->transactionProductRow($number, $transactionIds, $productIds));
        $transactionProductIds = $this->ids('transaction_products');

        $this->topUp('product_purchase_batches', fn (int $number) => [
            'product_id' => $this->randomValue($productIds),
            'transaction_product_id' => $this->randomValue($transactionProductIds),
            'purchased_quantity' => 20 + ($number % 80),
            'remaining_quantity' => 5 + ($number % 40),
            'unit_cost' => $faker->randomFloat(2, 50, 10000),
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ]);
        $batchIds = $this->ids('product_purchase_batches');

        $this->topUp('transaction_product_batch_allocations', fn (int $number) => [
            'transaction_product_id' => $this->randomValue($transactionProductIds),
            'product_purchase_batch_id' => $this->randomValue($batchIds),
            'quantity' => 1 + ($number % 10),
            'unit_cost' => $faker->randomFloat(2, 50, 10000),
            'total' => $faker->randomFloat(2, 100, 30000),
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ]);

        $this->topUp('client_debt_logs', fn (int $number) => [
            'client_id' => $this->randomValue($clientIds),
            'debt_before' => $faker->randomFloat(2, 0, 30000),
            'debt_after' => $faker->randomFloat(2, 0, 30000),
            'change_amount' => $faker->randomFloat(2, -5000, 5000),
            'source_type' => 'seed',
            'source_id' => $this->randomValue($transactionIds),
            'description' => "Bulk debt log {$number}",
            'created_by' => $this->randomValue($userIds),
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ]);

        $this->topUp('installment_contracts', fn (int $number) => $this->installmentContractRow($number, $clientIds, $productIds, $userIds));
        $contractIds = $this->ids('installment_contracts');

        $this->topUp('installments', fn (int $number) => [
            'due_date' => now()->addDays($number % 365)->toDateString(),
            'required_amount' => $faker->randomFloat(2, 250, 5000),
            'paid_amount' => $number % 3 === 0 ? $faker->randomFloat(2, 250, 5000) : 0,
            'status' => ['pending', 'paid', 'late'][$number % 3],
            'installment_contract_id' => $this->randomValue($contractIds),
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ]);
        $installmentIds = $this->ids('installments');

        $this->topUp('installment_payments', fn (int $number) => [
            'installment_id' => $this->randomValue($installmentIds),
            'transaction_id' => $this->randomValue($transactionIds),
            'amount' => $faker->randomFloat(2, 100, 5000),
            'payment_date' => now()->subDays($number % 365)->toDateString(),
            'paid_by' => $this->randomValue($userIds),
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ]);

        $this->topUp('associations', fn (int $number) => [
            'name' => "Bulk Association {$number}",
            'per_day' => (string) (50 + ($number % 200)),
            'total_members' => 10 + ($number % 30),
            'monthly_amount' => $faker->randomFloat(2, 500, 5000),
            'start_date' => now()->subDays($number % 365)->toDateString(),
            'end_date' => now()->addDays(30 + ($number % 365))->toDateString(),
            'status' => ['active', 'completed', 'paused'][$number % 3],
            'created_by' => $this->randomValue($userIds),
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ]);
        $associationIds = $this->ids('associations');

        $this->topUp('association_members', fn (int $number) => [
            'association_id' => $this->randomValue($associationIds),
            'transaction_id' => $this->randomValue($transactionIds),
            'client_id' => $this->randomValue($clientIds),
            'payout_order' => 1 + ($number % 50),
            'receive_date' => now()->addDays($number % 365)->toDateString(),
            'amount' => (string) $faker->randomFloat(2, 200, 5000),
            'has_received' => $number % 4 === 0,
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ]);
        $associationMemberIds = $this->ids('association_members');

        $this->topUp('association_payments', fn (int $number) => [
            'association_id' => $this->randomValue($associationIds),
            'transaction_id' => $this->randomValue($transactionIds),
            'member_id' => $this->randomValue($associationMemberIds),
            'amount' => $faker->randomFloat(2, 100, 5000),
            'payment_date' => now()->subDays($number % 365)->toDateString(),
            'status' => ['paid', 'pending', 'late'][$number % 3],
            'created_by' => $this->randomValue($userIds),
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ]);

        $this->topUp('iphones', fn (int $number) => $this->iphoneRow($number, $userIds));
        $iphoneIds = $this->ids('iphones');

        $this->topUp('iphone_logs', fn (int $number) => [
            'iphone_id' => $this->randomValue($iphoneIds),
            'transaction_id' => $this->randomValue($transactionIds),
            'payment_way_id' => $this->randomValue($paymentWayIds),
            'client_id' => $this->randomValue($clientIds),
            'action_type' => ['purchase', 'sale', 'expense', 'return'][$number % 4],
            'amount' => $faker->randomFloat(2, 100, 5000),
            'notes' => "Bulk iPhone log {$number}",
            'created_by' => $this->randomValue($userIds),
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ]);
    }

    private function seedPermissions(): void
    {
        $this->topUp('permissions', fn (int $number) => [
            'name' => "bulk_permission_{$number}",
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedRoles(): void
    {
        $this->topUp('roles', fn (int $number) => [
            'name' => "Bulk Role {$number}",
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedRolePermissions(): void
    {
        if (! $this->tableExists('role_has_permissions')) {
            return;
        }

        $roleIds = $this->ids('roles');
        $permissionIds = $this->ids('permissions');
        $existing = DB::table('role_has_permissions')->count();
        $missing = $this->targetCount - $existing;

        if ($missing <= 0 || empty($roleIds) || empty($permissionIds)) {
            return;
        }

        $rows = [];
        $offset = 0;

        while (count($rows) < $missing) {
            $rows[] = [
                'role_id' => $roleIds[$offset % count($roleIds)],
                'permission_id' => $permissionIds[($offset * 7) % count($permissionIds)],
            ];
            $offset++;
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('role_has_permissions')->insertOrIgnore($chunk);
        }
    }

    private function seedModelRoles(array $userIds): void
    {
        if (! $this->tableExists('model_has_roles')) {
            return;
        }

        $roleIds = $this->ids('roles');
        $existing = DB::table('model_has_roles')->count();
        $missing = $this->targetCount - $existing;

        if ($missing <= 0 || empty($roleIds)) {
            return;
        }

        $rows = [];

        for ($i = 0; $i < $missing; $i++) {
            $rows[] = [
                'role_id' => $roleIds[$i % count($roleIds)],
                'model_type' => User::class,
                'model_id' => $userIds[$i % count($userIds)],
            ];
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('model_has_roles')->insertOrIgnore($chunk);
        }
    }

    private function seedModelPermissions(array $userIds): void
    {
        if (! $this->tableExists('model_has_permissions')) {
            return;
        }

        $permissionIds = $this->ids('permissions');
        $existing = DB::table('model_has_permissions')->count();
        $missing = $this->targetCount - $existing;

        if ($missing <= 0 || empty($permissionIds)) {
            return;
        }

        $rows = [];

        for ($i = 0; $i < $missing; $i++) {
            $rows[] = [
                'permission_id' => $permissionIds[$i % count($permissionIds)],
                'model_type' => User::class,
                'model_id' => $userIds[$i % count($userIds)],
            ];
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('model_has_permissions')->insertOrIgnore($chunk);
        }
    }

    private function topUp(string $table, callable $factory): void
    {
        if (! $this->tableExists($table)) {
            return;
        }

        $existing = DB::table($table)->count();
        $missing = $this->targetCount - $existing;

        if ($missing <= 0) {
            return;
        }

        $rows = [];

        for ($i = 1; $i <= $missing; $i++) {
            $rows[] = $factory($existing + $i);
        }

        $this->insertChunks($table, $rows);
    }

    private function transactionRow(int $number, array $paymentWayIds, array $clientIds, array $productIds, array $userIds): array
    {
        $faker = FakerFactory::create();
        $amount = $faker->randomFloat(2, 50, 20000);
        $commission = $faker->randomFloat(2, 0, 250);
        $balanceBefore = $faker->randomFloat(2, 1000, 100000);
        $type = $number % 2 === 0 ? 'receive' : 'send';
        $balanceAfter = $type === 'receive' ? $balanceBefore + $amount + $commission : $balanceBefore - $amount - $commission;

        return [
            'payment_way_id' => $this->randomValue($paymentWayIds),
            'created_by' => $this->randomValue($userIds),
            'type' => $type,
            'amount' => $amount,
            'commission' => $commission,
            'notes' => "Bulk transaction {$number}",
            'attachment' => null,
            'client_id' => $this->randomValue($clientIds),
            'product_id' => $this->randomValue($productIds),
            'quantity' => 1 + ($number % 5),
            'balance_before_transaction' => (string) $balanceBefore,
            'balance_after_transaction' => (string) $balanceAfter,
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ];
    }

    private function transactionProductRow(int $number, array $transactionIds, array $productIds): array
    {
        $faker = FakerFactory::create();
        $quantity = 1 + ($number % 8);
        $unitPrice = $faker->randomFloat(2, 50, 10000);

        return [
            'transaction_id' => $this->randomValue($transactionIds),
            'product_id' => $this->randomValue($productIds),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total' => $unitPrice * $quantity,
            'cost_total' => $unitPrice * $quantity * 0.75,
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ];
    }

    private function installmentContractRow(int $number, array $clientIds, array $productIds, array $userIds): array
    {
        $faker = FakerFactory::create();
        $productPrice = $faker->randomFloat(2, 1000, 30000);
        $downPayment = $faker->randomFloat(2, 0, $productPrice * 0.3);
        $remaining = $productPrice - $downPayment;
        $interestRate = $faker->randomFloat(2, 0, 18);
        $interestAmount = $remaining * ($interestRate / 100);
        $total = $remaining + $interestAmount;
        $count = 3 + ($number % 24);

        return [
            'product_price' => $productPrice,
            'down_payment' => $downPayment,
            'remaining_amount' => $remaining,
            'installment_count' => $count,
            'interest_rate' => $interestRate,
            'interest_amount' => $interestAmount,
            'total_amount' => $total,
            'installment_amount' => $total / $count,
            'start_date' => now()->subDays($number % 365)->toDateString(),
            'client_id' => $this->randomValue($clientIds),
            'product_id' => $this->randomValue($productIds),
            'created_by' => $this->randomValue($userIds),
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ];
    }

    private function iphoneRow(int $number, array $userIds): array
    {
        $faker = FakerFactory::create();
        $purchaseSar = $faker->randomFloat(2, 1500, 6500);
        $purchaseEgp = $purchaseSar * 13.2;
        $expenses = $faker->randomFloat(2, 100, 1500);
        $total = $purchaseEgp + $expenses;
        $sale = $total + $faker->randomFloat(2, 500, 5000);

        return [
            'device_type' => ['iPhone 13', 'iPhone 14', 'iPhone 15', 'iPhone 16'][$number % 4],
            'device_details' => "Bulk device {$number}",
            'purchase_price_sar' => $purchaseSar,
            'currency' => 'SAR',
            'purchase_price_egp' => $purchaseEgp,
            'extra_expenses' => $expenses,
            'total_purchase_with_expenses' => $total,
            'sale_price_egp' => $sale,
            'net_profit_after_sale' => $sale - $total,
            'status' => ['available', 'sold', 'reserved'][$number % 3],
            'created_by' => $this->randomValue($userIds),
            'created_at' => $this->date($number),
            'updated_at' => $this->date($number),
        ];
    }

    private function ids(string $table): array
    {
        if (! $this->tableExists($table) || ! Schema::hasColumn($table, 'id')) {
            return [];
        }

        return DB::table($table)->pluck('id')->all();
    }

    private function randomId(string $table): ?int
    {
        $ids = $this->ids($table);

        return empty($ids) ? null : $this->randomValue($ids);
    }

    private function randomValue(array $values): mixed
    {
        return $values[array_rand($values)];
    }

    private function tableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }

    private function date(int $number): Carbon
    {
        return now()->subMinutes($number);
    }

    private function insertChunks(string $table, array $rows, ?string $connection = null): void
    {
        if (empty($rows)) {
            return;
        }

        $tableName = Str::after($table, '.');
        $columns = $connection
            ? Schema::connection($connection)->getColumnListing($tableName)
            : Schema::getColumnListing($tableName);
        $query = $connection ? DB::connection($connection)->table(Str::after($table, '.')) : DB::table($table);

        foreach (array_chunk($rows, 500) as $chunk) {
            $query->insert(array_map(
                fn (array $row) => array_intersect_key($row, array_flip($columns)),
                $chunk
            ));
        }
    }
}
