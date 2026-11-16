<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Tenant;
use App\Models\Umrah\Booking;
use App\Models\Umrah\Notification;
use App\Models\Umrah\Traveler;
use App\Models\Umrah\Trip;
use App\Models\User;
use App\Services\AdminTenantService;
use App\Services\PhoneNumberService;
use App\Services\UmrahNotificationService;
use App\Services\UmrahService;
use App\Support\UmrahMoney;
use Database\Seeders\UmrahPermissionSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UmrahTest extends TestCase
{
    private string $tenantFile;

    private User $user;

    private Tenant $tenant;

    private UmrahService $service;

    protected function setUp(): void
    {
        parent::setUp();
        // Every connection is replaced before any query. No existing database is migrated or reset.
        $this->tenantFile = tempnam(sys_get_temp_dir(), 'umrah-test-');
        config([
            'database.default' => 'tenant',
            'database.connections.central' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true],
            'database.connections.tenant' => ['driver' => 'sqlite', 'database' => $this->tenantFile, 'foreign_key_constraints' => true],
            'tenancy.database.central_connection' => 'central', 'tenancy.bootstrappers' => [],
            'cache.default' => 'array', 'permission.cache.store' => 'array', 'rate_limiting.system.enabled' => false,
            'app.locale' => 'ar',
        ]);
        DB::purge('central');
        DB::purge('tenant');
        Http::preventStrayRequests();
        Schema::connection('central')->create('tenants', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('name');
            $t->string('domain');
            $t->json('data')->nullable();
            $t->timestamps();
        });
        // Real cross-database user model; the tenant database deliberately has no users table.
        Schema::connection('central')->create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->string('password');
            $t->string('tenant_id');
            $t->boolean('is_active')->default(true);
            $t->string('whatsapp_api_token')->nullable();
            $t->rememberToken();
            $t->timestamps();
        });
        (require database_path('migrations/2026_11_16_000001_add_umrah_access_settings.php'))->up();
        (require database_path('migrations/2026_11_16_000002_add_company_type_to_tenants.php'))->up();
        foreach (['2025_09_13_111754_create_permission_tables.php', '2026_11_16_000001_create_umrah_tables.php', '2026_11_16_000002_create_umrah_notifications.php'] as $migration) {
            (require database_path('migrations/tenant/'.$migration))->up();
        }
        $this->tenant = Tenant::withoutEvents(fn () => Tenant::create(['id' => 'test-a', 'name' => 'Test office', 'domain' => 'test.example', 'company_type' => 'umrah']));
        tenancy()->initialize($this->tenant);
        app(UmrahPermissionSeeder::class)->run();
        $this->user = User::create(['name' => 'Test manager', 'email' => 'manager@test.example', 'password' => 'test-password', 'tenant_id' => $this->tenant->id, 'is_active' => true]);
        $this->user->assignRole('umrah_manager');
        $this->actingAs($this->user->fresh(), 'web');
        $this->service = app(UmrahService::class);
    }

    protected function tearDown(): void
    {
        if (isset($this->tenantFile)) {
            tenancy()->end();
            DB::disconnect('tenant');
            DB::disconnect('central');
            @unlink($this->tenantFile);
        }
        parent::tearDown();
    }

    private function fixture(int $capacity = 10, string $priceAmount = '35000.00'): array
    {
        $trip = Trip::create(['name' => 'عمرة اختبار', 'departure_date' => '2027-01-10', 'return_date' => '2027-01-20', 'capacity' => $capacity, 'status' => 'open', 'created_by' => $this->user->id]);
        $program = $trip->programs()->create(['name' => 'اقتصادي', 'makkah_nights' => 5, 'madinah_nights' => 5]);
        $price = $program->prices()->create(['label' => 'رباعية', 'amount' => $priceAmount, 'currency' => 'EGP']);
        $traveler = Traveler::create(['name' => 'مسافر اختبار', 'phone' => '+201001234567', 'created_by' => $this->user->id, 'whatsapp_enabled' => true]);

        return [$trip, $price, $traveler];
    }

    private function book(?array $fixture = null): Booking
    {
        [$trip, $price, $traveler] = $fixture ?? $this->fixture();

        return $this->service->createBooking(['trip_id' => $trip->id, 'traveler_id' => $traveler->id, 'program_price_id' => $price->id]);
    }

    private function payment(Booking $booking, string $amount, string $type = 'collection', string $status = 'confirmed', ?string $token = null)
    {
        return $this->service->addPayment($booking->id, ['amount' => $amount, 'type' => $type, 'status' => $status,
            'method' => 'cash', 'paid_at' => today()->format('Y-m-d'), 'submission_token' => $token ?? (string) Str::uuid()]);
    }

    public function test_booking_keeps_original_price_and_capacity_is_enforced(): void
    {
        $fixture = $this->fixture(1);
        $booking = $this->book($fixture);
        $fixture[1]->update(['amount' => '99999.00']);
        $this->assertSame('35000.00', $booking->fresh()->total_amount);
        $traveler = Traveler::create(['name' => 'آخر', 'phone' => '01001234567', 'created_by' => $this->user->id]);
        $fixture[2] = $traveler;
        $this->expectException(ValidationException::class);
        $this->book($fixture);
    }

    public function test_price_from_another_trip_is_rejected(): void
    {
        $a = $this->fixture();
        $b = $this->fixture();
        $a[1] = $b[1];
        $this->expectException(ValidationException::class);
        $this->book($a);
    }

    public function test_collection_is_exact_and_idempotent_with_one_notification(): void
    {
        $booking = $this->book($this->fixture(10, '100.30'));
        $token = (string) Str::uuid();
        $first = $this->payment($booking, '10.10', token: $token);
        $again = $this->payment($booking, '10.10', token: $token);
        $this->payment($booking, '20.20');
        $this->assertSame($first->id, $again->id);
        $this->assertSame(3030, $booking->fresh()->paidCents());
        $this->assertSame(7000, $booking->fresh()->balanceCents());
        $this->assertSame(2, $booking->payments()->count());
        $this->assertSame(2, Notification::where('event', 'payment_confirmed')->count());
    }

    public function test_overpayment_is_rejected(): void
    {
        $booking = $this->book();
        $this->expectException(ValidationException::class);
        $this->payment($booking, '35000.01');
    }

    public function test_pending_payment_does_not_reduce_balance_and_can_be_confirmed_once(): void
    {
        $booking = $this->book();
        $payment = $this->payment($booking, '10000', status: 'pending');
        $this->assertSame(3500000, $booking->fresh()->balanceCents());
        $this->service->reviewPayment($booking->id, $payment->id, true);
        $this->assertSame(2500000, $booking->fresh()->balanceCents());
        $this->expectException(ValidationException::class);
        $this->service->reviewPayment($booking->id, $payment->id, true);
    }

    public function test_cancellation_and_refund_keep_financial_history(): void
    {
        $booking = $this->book();
        $this->payment($booking, '10000');
        $this->service->settle($booking->id, ['total_amount' => '2000', 'reason' => 'إلغاء بناء على طلب العميل'], true);
        $this->assertSame(-800000, $booking->fresh()->balanceCents());
        $this->payment($booking, '8000', 'refund');
        $this->assertSame(0, $booking->fresh()->balanceCents());
        $this->assertSame(2, $booking->payments()->count());
        $this->assertSame('cancelled', $booking->fresh()->status);
        $this->expectException(ValidationException::class);
        $this->payment($booking, '0.01', 'refund');
    }

    public function test_schedule_overdue_and_adjustment_audit(): void
    {
        $booking = $this->book();
        $this->service->addSchedule($booking->id, ['amount' => '10000', 'due_date' => today()->subDay()->format('Y-m-d')]);
        $this->payment($booking, '4000');
        $this->assertSame(600000, $booking->fresh()->overdueCents());
        $this->service->settle($booking->id, ['total_amount' => '30000', 'reason' => 'خصم'], false);
        $this->assertSame(0, $booking->schedules()->count());
        $this->assertCount(1, $booking->logs()->where('action', 'agreement_adjusted')->first()->data['previous_schedules']);
    }

    public function test_dashboard_and_all_detail_views_render(): void
    {
        $booking = $this->book();
        $payment = $this->payment($booking, '100');
        foreach (['/dashboard/umrah', '/dashboard/umrah/trips/'.$booking->trip_id,
            '/dashboard/umrah/travelers', '/dashboard/umrah/travelers/'.$booking->traveler_id,
            '/dashboard/umrah/bookings/'.$booking->id, '/dashboard/umrah/receipts/'.$payment->id,
            '/dashboard/umrah/reports'] as $url) {
            $this->withSession(['tenant_id' => $this->tenant->id])->get($url)->assertOk();
        }
    }

    public function test_company_type_grants_umrah_access_but_general_company_is_rejected(): void
    {
        $this->user->syncRoles([]);
        $this->actingAs($this->user->fresh());
        $this->withSession(['tenant_id' => $this->tenant->id])->get('/dashboard/umrah')->assertOk();
        $this->user->assignRole('umrah_manager');
        $this->actingAs($this->user->fresh());
        $this->tenant->forceFill(['company_type' => 'general', 'umrah_enabled' => true])->save();
        tenancy()->end();
        $this->get('/dashboard/umrah')->assertForbidden();
    }

    public function test_session_cannot_select_another_company(): void
    {
        $this->withSession(['tenant_id' => 'another-company'])->get('/dashboard/umrah')->assertForbidden();
    }

    public function test_all_umrah_company_users_cannot_mutate_general_module_despite_legacy_flags(): void
    {
        $this->user->forceFill(['umrah_only' => false, 'default_module' => 'general'])->save();
        $this->actingAs($this->user->fresh());
        $this->withSession(['tenant_id' => $this->tenant->id])->get('/dashboard')->assertRedirect(route('umrah.index'));
        $this->post('/dashboard/categories', ['name' => 'not allowed'])->assertForbidden();
    }

    public function test_whatsapp_is_post_commit_and_records_provider_acceptance(): void
    {
        $this->user->forceFill(['whatsapp_api_token' => 'fake-test-token'])->save();
        Http::fake(['*/api/external/messages/send' => Http::response(['success' => true, 'messageId' => 'test-message'])]);
        $fixture = $this->fixture();
        DB::beginTransaction();
        $this->book($fixture);
        Http::assertNothingSent();
        DB::commit();
        Http::assertSentCount(1);
        $this->assertSame('accepted', Notification::first()->status);
        app(UmrahNotificationService::class)->deliver(Notification::first()->id);
        Http::assertSentCount(1);
    }

    public function test_whatsapp_failure_does_not_rollback_booking_and_no_whatsapp_is_recorded(): void
    {
        $this->user->forceFill(['whatsapp_api_token' => 'fake-test-token'])->save();
        Http::fake(['*/api/external/messages/send' => Http::response(['success' => false, 'errorCode' => 'NUMBER_NOT_REGISTERED'], 422)]);
        $booking = $this->book();
        $this->assertNotNull($booking->fresh());
        $this->assertSame('unavailable', Notification::first()->status);
    }

    public function test_rolled_back_events_do_not_send_or_leave_notifications(): void
    {
        $fixture = $this->fixture();
        DB::beginTransaction();
        $this->book($fixture);
        DB::rollBack();
        $this->assertSame(0, Notification::count());
        Http::assertNothingSent();
    }

    public function test_login_uses_umrah_home_including_existing_session(): void
    {
        $this->user->forceFill(['umrah_only' => false, 'default_module' => 'general'])->save();
        auth('web')->logout();
        $this->withSession(['url.intended' => '/dashboard/categories'])->post('/login', [
            'email' => $this->user->email, 'password' => 'test-password',
        ])->assertRedirect(route('umrah.index'));
        $this->get('/')->assertRedirect(route('umrah.index'));
    }

    public function test_readonly_role_cannot_collect_or_download_private_documents(): void
    {
        $booking = $this->book();
        $this->user->syncRoles(['umrah_viewer']);
        $this->actingAs($this->user->fresh());
        $this->withSession(['tenant_id' => $this->tenant->id])->post('/dashboard/umrah/bookings/'.$booking->id.'/payments', [
            'amount' => '100', 'type' => 'collection', 'status' => 'confirmed', 'method' => 'cash',
            'paid_at' => today()->format('Y-m-d'), 'submission_token' => (string) Str::uuid(),
        ])->assertForbidden();
        $this->get('/dashboard/umrah/documents/1')->assertForbidden();
    }

    public function test_admin_settings_render_and_role_assignment_is_scoped(): void
    {
        auth('web')->logout();
        auth('admin')->setUser(new Admin(['name' => 'Test administrator']));
        $this->get('/admin/tenants/'.$this->tenant->id.'/umrah')->assertOk();
        $this->put('/admin/tenants/'.$this->tenant->id.'/umrah/users/'.$this->user->id, [
            'umrah_role' => 'umrah_bookings',
        ])->assertRedirect();
        $this->assertSame('umrah.index', $this->user->fresh()->umrahHomeRoute());
        $this->assertTrue($this->user->fresh()->hasPermissionTo('umrah_bookings_manage'));
        $this->assertFalse($this->user->fresh()->hasPermissionTo('umrah_refunds_approve'));
    }

    public function test_invalid_admin_role_does_not_remove_existing_roles(): void
    {
        auth('web')->logout();
        auth('admin')->setUser(new Admin(['name' => 'Test administrator']));
        $this->putJson('/admin/tenants/'.$this->tenant->id.'/umrah/users/'.$this->user->id, [
            'umrah_role' => 'not-a-real-role',
        ])->assertStatus(422);
        $this->assertTrue($this->user->fresh()->hasPermissionTo('umrah_refunds_approve'));
        $this->assertSame($this->tenant->id, tenant('id'));
    }

    public function test_disabled_whatsapp_and_missing_sender_token_are_documented(): void
    {
        $fixture = $this->fixture();
        $fixture[2]->update(['whatsapp_enabled' => false]);
        $this->book($fixture);
        $this->assertSame('disabled_by_customer', Notification::first()->result_code);
        $fixture = $this->fixture();
        $this->book($fixture);
        $this->assertSame('missing_sender_token', Notification::orderByDesc('id')->first()->result_code);
        Http::assertNothingSent();
    }

    public function test_ambiguous_whatsapp_result_is_not_retried_automatically(): void
    {
        $this->user->forceFill(['whatsapp_api_token' => 'fake-test-token'])->save();
        Http::fake(['*/api/external/messages/send' => Http::response([], 503)]);
        $booking = $this->book();
        $notification = Notification::first();
        $this->assertSame('unknown', $notification->status);
        $this->assertNotNull($booking->fresh());
        app(UmrahNotificationService::class)->deliver($notification->id);
        Http::assertSentCount(1);
    }

    public function test_notification_outbox_cannot_use_a_sender_from_another_tenant(): void
    {
        $booking = $this->book();
        $notification = Notification::first();
        $other = User::create(['name' => 'Other', 'email' => 'other@test.example', 'password' => 'password', 'tenant_id' => 'another-company', 'is_active' => true, 'whatsapp_api_token' => 'must-not-be-used']);
        $notification->update(['created_by' => $other->id, 'status' => 'pending']);
        app(UmrahNotificationService::class)->deliver($notification->id);
        $this->assertSame('missing_sender_token', $notification->fresh()->result_code);
        Http::assertNothingSent();
    }

    public function test_program_change_preserves_payments_and_audits_old_agreement(): void
    {
        $fixture = $this->fixture();
        $booking = $this->book($fixture);
        $this->payment($booking, '10000');
        $price = $fixture[1]->program->prices()->create(['label' => 'ثنائية', 'amount' => '45000.00', 'currency' => 'EGP']);
        $this->service->changeProgram($booking->id, ['program_price_id' => $price->id, 'reason' => 'طلب العميل']);
        $this->assertSame('45000.00', $booking->fresh()->total_amount);
        $this->assertSame(3500000, $booking->fresh()->balanceCents());
        $this->assertSame('35000.00', $booking->logs()->where('action', 'program_changed')->first()->data['previous_total']);
        $this->assertSame(1, $booking->payments()->count());
    }

    public function test_another_tenant_cannot_read_booking_or_document_ids(): void
    {
        $booking = $this->book();
        $document = $booking->traveler->documents()->create(['label' => 'خاص', 'path' => 'tenants/test-a/private.pdf', 'original_name' => 'private.pdf', 'created_by' => $this->user->id]);
        $otherFile = tempnam(sys_get_temp_dir(), 'umrah-isolation-');
        try {
            tenancy()->end();
            DB::disconnect('tenant');
            config(['database.connections.tenant.database' => $otherFile]);
            DB::purge('tenant');
            foreach (['2025_09_13_111754_create_permission_tables.php', '2026_11_16_000001_create_umrah_tables.php', '2026_11_16_000002_create_umrah_notifications.php'] as $migration) {
                (require database_path('migrations/tenant/'.$migration))->up();
            }
            $otherTenant = Tenant::withoutEvents(fn () => Tenant::create(['id' => 'test-b', 'name' => 'Other office', 'domain' => 'other.example', 'company_type' => 'umrah']));
            tenancy()->initialize($otherTenant);
            app(UmrahPermissionSeeder::class)->run();
            $otherUser = User::create(['name' => 'Other manager', 'email' => 'other@other.example', 'password' => 'password', 'tenant_id' => 'test-b', 'is_active' => true]);
            $otherUser->assignRole('umrah_manager');
            $this->actingAs($otherUser->fresh());
            $this->withSession(['tenant_id' => 'test-b'])->get('/dashboard/umrah/bookings/'.$booking->id)->assertNotFound();
            $this->get('/dashboard/umrah/documents/'.$document->id)->assertNotFound();
            $this->assertSame(0, Booking::count());
        } finally {
            tenancy()->end();
            DB::disconnect('tenant');
            config(['database.connections.tenant.database' => $this->tenantFile]);
            DB::purge('tenant');
            @unlink($otherFile);
        }
    }

    public function test_booking_closes_only_after_return_and_settlement(): void
    {
        $booking = $this->book();
        $this->payment($booking, '35000');
        $this->service->procedures($booking->id, ['visa_status' => 'issued', 'ticket_status' => 'issued', 'travel_status' => 'returned']);
        $this->service->complete($booking->id);
        $this->assertSame('completed', $booking->fresh()->status);
        $this->assertSame(1, Notification::where('event', 'booking_completed')->count());
        $this->expectException(ValidationException::class);
        $this->service->settle($booking->id, ['total_amount' => '100', 'reason' => 'must not change closed booking'], false);
    }

    public function test_json_decimal_amount_is_not_coerced_to_integer(): void
    {
        $booking = $this->book($this->fixture(10, '100.30'));
        $this->withSession(['tenant_id' => $this->tenant->id])->postJson('/dashboard/umrah/bookings/'.$booking->id.'/payments', [
            'amount' => 10.10, 'type' => 'collection', 'status' => 'confirmed', 'method' => 'cash',
            'paid_at' => today()->format('Y-m-d'), 'submission_token' => (string) Str::uuid(),
        ])->assertRedirect();
        $this->assertSame(1010, $booking->fresh()->paidCents());
        $this->assertSame(9020, $booking->fresh()->balanceCents());
    }

    public function test_company_creation_passes_selected_type_to_provisioning(): void
    {
        auth('web')->logout();
        auth('admin')->setUser(new Admin(['name' => 'Test administrator']));
        $service = \Mockery::mock(AdminTenantService::class);
        $service->shouldReceive('createTenant')->once()->with('New Umrah', 'new.example', 'umrah')->andReturn($this->tenant);
        $service->shouldReceive('createTenant')->once()->with('New General', 'general.example', 'general')->andReturn($this->tenant);
        $this->app->instance(AdminTenantService::class, $service);
        $this->get('/admin/tenants/create')->assertOk()->assertSee('name="company_type"', false);
        $this->post('/admin/tenants', ['name' => 'New Umrah', 'domain' => 'new.example', 'company_type' => 'umrah'])->assertRedirect(route('admin.dashboard'));
        $this->post('/admin/tenants', ['name' => 'New General', 'domain' => 'general.example', 'company_type' => 'general'])->assertRedirect(route('admin.dashboard'));
        $this->postJson('/admin/tenants', ['name' => 'Invalid', 'domain' => 'invalid.example'])->assertUnprocessable()->assertJsonValidationErrors('company_type');
        $this->postJson('/admin/tenants', ['name' => 'Invalid', 'domain' => 'invalid.example', 'company_type' => 'mixed'])->assertUnprocessable()->assertJsonValidationErrors('company_type');
    }

    public function test_new_employee_in_umrah_company_enters_umrah_without_individual_toggle(): void
    {
        auth('web')->logout();
        auth('admin')->setUser(new Admin(['name' => 'Test administrator']));
        $this->post('/admin/tenants/'.$this->tenant->id.'/users', [
            'name' => 'New employee', 'email' => 'new@test.example', 'password' => 'test-password', 'is_active' => 1,
        ])->assertRedirect();
        $employee = User::where('email', 'new@test.example')->firstOrFail();
        auth('admin')->logout();
        $this->post('/login', ['email' => $employee->email, 'password' => 'test-password'])->assertRedirect(route('umrah.index'));
        $this->get('/dashboard/umrah')->assertOk();
        $this->getJson('/dashboard/analytics')->assertForbidden();
        $this->post('/dashboard/transactions', [])->assertForbidden();
        $this->assertSame('umrah', $employee->tenant->company_type);
    }

    public function test_general_company_ignores_old_employee_and_feature_switches(): void
    {
        $this->tenant->forceFill(['company_type' => 'general', 'umrah_enabled' => true])->save();
        $this->user->forceFill(['umrah_only' => true, 'default_module' => 'umrah'])->save();
        tenancy()->end();
        auth('web')->logout();
        $this->withSession(['url.intended' => '/dashboard/umrah'])->post('/login', ['email' => $this->user->email, 'password' => 'test-password'])->assertRedirect(route('dashboard.index'));
        $this->get('/dashboard/umrah')->assertForbidden();
        $this->post('/dashboard/umrah/trips', [])->assertForbidden();
        auth('web')->logout();
        auth('admin')->setUser(new Admin(['name' => 'Test administrator']));
        $this->get('/admin/tenants/'.$this->tenant->id.'/umrah')->assertNotFound();
        $this->put('/admin/tenants/'.$this->tenant->id.'/umrah/users/'.$this->user->id, ['umrah_role' => 'umrah_manager'])->assertNotFound();
    }

    public function test_general_role_cannot_be_assigned_to_umrah_company_employee(): void
    {
        $permission = Permission::findOrCreate('transactions_index', 'web');
        Role::findOrCreate('General accountant', 'web')->givePermissionTo($permission);
        auth('web')->logout();
        auth('admin')->setUser(new Admin(['name' => 'Test administrator']));
        $this->postJson('/admin/tenants/'.$this->tenant->id.'/users', [
            'name' => 'Invalid role employee', 'email' => 'invalid@test.example', 'password' => 'test-password', 'role' => 'General accountant', 'is_active' => 1,
        ])->assertUnprocessable()->assertJsonValidationErrors('role');
        $this->assertFalse(User::where('email', 'invalid@test.example')->exists());
        $this->get('/admin/tenants/'.$this->tenant->id.'/users/create')->assertOk()->assertDontSee('General accountant');
    }

    public function test_company_type_migration_preserves_previously_enabled_companies(): void
    {
        $migration = require database_path('migrations/2026_11_16_000002_add_company_type_to_tenants.php');
        $migration->down();
        DB::connection('central')->table('tenants')->where('id', $this->tenant->id)->update(['umrah_enabled' => true]);
        DB::connection('central')->table('tenants')->insert(['id' => 'existing-general', 'name' => 'Existing', 'domain' => 'existing.example', 'umrah_enabled' => false]);
        $migration->up();
        $this->assertSame('umrah', $this->tenant->fresh()->company_type);
        $this->assertSame('general', Tenant::find('existing-general')->company_type);
    }

    public function test_traveler_country_code_picker_matches_client_options(): void
    {
        $options = app(PhoneNumberService::class)->countryCodeOptions();
        $this->assertContains('+20', array_column($options, 'code'));
        $this->assertContains('+966', array_column($options, 'code'));
        $this->withSession(['tenant_id' => $this->tenant->id])->get('/dashboard/umrah/travelers')
            ->assertOk()->assertViewHas('countryCodeOptions', $options)
            ->assertSee('name="country_code"', false)->assertSee('name="phone_number"', false);
    }

    public function test_traveler_phone_is_saved_internationally_and_used_for_whatsapp(): void
    {
        foreach ([['+20', '01001234567', '+201001234567'], ['+966', '0501234567', '+966501234567']] as [$code, $national, $expected]) {
            $response = $this->withSession(['tenant_id' => $this->tenant->id])->post('/dashboard/umrah/travelers', [
                'name' => 'Phone test', 'country_code' => $code, 'phone_number' => $national, 'whatsapp_enabled' => 1,
            ]);
            $traveler = Traveler::latest('id')->firstOrFail();
            $response->assertRedirect(route('umrah.travelers.show', $traveler->id));
            $this->assertSame($expected, $traveler->phone);
            $this->assertSame($expected, Notification::where('traveler_id', $traveler->id)->firstOrFail()->phone);
        }
    }

    public function test_edit_traveler_restores_country_code_and_updates_phone(): void
    {
        [, , $traveler] = $this->fixture();
        $traveler->update(['phone' => '+966501234567']);
        $this->withSession(['tenant_id' => $this->tenant->id])->get('/dashboard/umrah/travelers/'.$traveler->id)
            ->assertOk()->assertSee('value="+966" selected', false)->assertSee('value="501234567"', false);
        $this->put('/dashboard/umrah/travelers/'.$traveler->id, [
            'name' => $traveler->name, 'country_code' => '+20', 'phone_number' => '01001234567', 'whatsapp_enabled' => 1,
        ])->assertRedirect();
        $this->assertSame('+201001234567', $traveler->fresh()->phone);
    }

    public function test_invalid_or_missing_country_code_does_not_save_traveler(): void
    {
        foreach ([
            ['country_code' => '+20', 'phone_number' => '0'],
            ['country_code' => '+20', 'phone_number' => 'invalid'],
            ['phone_number' => '01001234567'],
            ['country_code' => '+20', 'phone_number' => '+966501234567'],
        ] as $phoneData) {
            $this->withSession(['tenant_id' => $this->tenant->id])->postJson('/dashboard/umrah/travelers', $phoneData + [
                'name' => 'Invalid phone', 'whatsapp_enabled' => 1,
            ])->assertUnprocessable();
        }
        $this->assertSame(0, Traveler::count());
        $this->assertSame(0, Notification::count());
        Http::assertNothingSent();
    }

    public function test_full_number_with_matching_code_is_not_prefixed_twice(): void
    {
        $service = app(PhoneNumberService::class);
        $this->assertSame('+201001234567', $service->international('+20', '+20 100 123 4567'));
        $this->assertSame('+966501234567', $service->international('+966', '00966501234567'));
        $this->assertSame(['country_code' => '+20', 'phone_number' => '1001234567'], $service->split('01001234567'));
    }

    public function test_traveler_agreed_price_accepts_multiple_unequal_payments(): void
    {
        [$trip, $price, $traveler] = $this->fixture();
        $this->withSession(['tenant_id' => $this->tenant->id])->post('/dashboard/umrah/bookings', [
            'trip_id' => $trip->id, 'program_price_id' => $price->id, 'traveler_id' => $traveler->id,
            'agreed_amount' => '32000', 'price_adjustment_reason' => 'Family discount',
        ])->assertRedirect();
        $booking = Booking::firstOrFail();
        $this->assertSame('32000.00', $booking->total_amount);
        $this->assertSame('35000.00', $booking->agreement['price']);
        $this->assertSame('32000.00', $booking->agreement['agreed_amount']);
        $this->assertSame('Family discount', $booking->agreement['price_adjustment_reason']);
        $this->assertSame(0, $booking->payments()->count());
        $price->update(['amount' => '40000.00']);
        $this->assertSame('32000.00', $booking->fresh()->total_amount);
        $this->payment($booking, '5000');
        $this->assertSame(2700000, $booking->fresh()->balanceCents());
        $this->payment($booking, '7000');
        $this->assertSame(2000000, $booking->fresh()->balanceCents());
        $this->payment($booking, '20000');
        $this->assertSame(0, $booking->fresh()->balanceCents());
        $this->assertSame(3, $booking->payments()->count());
        $this->assertSame('32000.00', $booking->logs()->where('action', 'booking_created')->firstOrFail()->data['agreed_amount']);
        $message = Notification::where('event', 'booking_created')->firstOrFail()->message;
        $this->assertStringContainsString('32000.00', $message);
        $this->assertStringNotContainsString('35000.00', $message);
        $this->get('/dashboard/umrah/bookings/'.$booking->id)->assertOk()->assertSee('32000.00')->assertSee('35000.00');
    }

    public function test_custom_agreed_price_can_be_higher_than_the_program_price_or_zero(): void
    {
        foreach (['38000.25', '0'] as $amount) {
            [$trip, $price, $traveler] = $this->fixture();
            $this->withSession(['tenant_id' => $this->tenant->id])->postJson('/dashboard/umrah/bookings', [
                'trip_id' => $trip->id, 'program_price_id' => $price->id, 'traveler_id' => $traveler->id,
                'agreed_amount' => $amount,
            ])->assertRedirect();
            $booking = Booking::latest('id')->firstOrFail();
            $this->assertSame(UmrahMoney::cents($amount), $booking->balanceCents());
            $this->assertSame('35000.00', $price->fresh()->amount);
        }
    }

    public function test_blank_agreed_price_uses_selected_tier_price(): void
    {
        [$trip, $price, $traveler] = $this->fixture();
        $this->withSession(['tenant_id' => $this->tenant->id])->post('/dashboard/umrah/bookings', [
            'trip_id' => $trip->id, 'program_price_id' => $price->id, 'traveler_id' => $traveler->id, 'agreed_amount' => '',
        ])->assertRedirect();
        $this->assertSame('35000.00', Booking::firstOrFail()->total_amount);
    }

    public function test_invalid_agreed_prices_cannot_create_bookings(): void
    {
        [$trip, $price, $traveler] = $this->fixture();
        foreach (['-1', '1.001', 'invalid', '10000000000.00'] as $amount) {
            $this->withSession(['tenant_id' => $this->tenant->id])->postJson('/dashboard/umrah/bookings', [
                'trip_id' => $trip->id, 'program_price_id' => $price->id, 'traveler_id' => $traveler->id,
                'agreed_amount' => $amount,
            ])->assertUnprocessable()->assertJsonValidationErrors('agreed_amount');
        }
        $this->assertSame(0, Booking::count());
        $this->assertSame(0, Notification::count());
    }

    public function test_collection_limit_uses_agreed_price_not_catalog_price(): void
    {
        [$trip, $price, $traveler] = $this->fixture();
        $booking = $this->service->createBooking([
            'trip_id' => $trip->id, 'program_price_id' => $price->id, 'traveler_id' => $traveler->id, 'agreed_amount' => '10000',
        ]);
        $this->payment($booking, '9000');
        $this->expectException(ValidationException::class);
        $this->payment($booking, '1000.01');
    }
}
