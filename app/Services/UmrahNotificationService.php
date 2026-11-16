<?php

namespace App\Services;

use App\Models\Umrah\Booking;
use App\Models\Umrah\BookingLog;
use App\Models\Umrah\Notification;
use App\Models\Umrah\Traveler;
use App\Models\User;
use App\Support\UmrahMoney;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class UmrahNotificationService
{
    public function bookingEvent(Booking $booking, BookingLog $log): void
    {
        $labels = [
            'booking_created' => 'تم تأكيد حجز العمرة', 'agreement_adjusted' => 'تم تعديل اتفاق الحجز',
            'program_changed' => 'تم تغيير برنامج الحجز', 'booking_cancelled' => 'تم إلغاء الحجز وتسوية المستحق',
            'payment_pending' => 'تم تسجيل دفعة قيد المراجعة ولم تُحتسب في المدفوع بعد',
            'payment_confirmed' => 'تم تأكيد استلام دفعتك', 'payment_void' => 'تم رفض الدفعة المعلقة',
            'refund_created' => 'تم تسجيل رد مبلغ إليك', 'schedule_added' => 'تم تحديد موعد سداد',
            'procedures_updated' => 'تم تحديث إجراءات سفرك',
            'booking_completed' => 'تم إغلاق حجزك بعد العودة وتسوية الحساب',
        ];
        $message = ($labels[$log->action] ?? 'تم تحديث حجزك')."\nرقم الحجز: ".$booking->reference
            ."\nالرحلة: ".$booking->trip->name."\nالبرنامج: ".data_get($booking->agreement, 'program.name')
            .' - '.data_get($booking->agreement, 'label');
        if (isset($log->data['amount'])) {
            $message .= "\nمبلغ العملية: ".$log->data['amount'].' '.$booking->currency;
        }
        if (isset($log->data['receipt'])) {
            $message .= "\nمرجع العملية: ".$log->data['receipt'];
        }
        if (isset($log->data['due_date'])) {
            $message .= "\nموعد السداد: ".$log->data['due_date'];
        }
        if (isset($log->data['reason'])) {
            $message .= "\nالسبب: ".$log->data['reason'];
        }
        if ($log->action === 'procedures_updated') {
            foreach ($log->data['after'] as $key => $value) {
                $message .= "\n".__('umrah.'.$key).': '.__('umrah.'.$value);
            }
        }
        $message .= "\nالمستحق: ".$booking->total_amount.' '.$booking->currency
            ."\nصافي المدفوع: ".UmrahMoney::decimal($booking->paidCents())
            ."\nالرصيد: ".UmrahMoney::decimal($booking->balanceCents())
            ."\nالرصيد الموجب متبقٍ عليك، والسالب مستحق لك.";
        $this->record($booking->traveler, $log->action, 'booking-log:'.$log->id, $message);
    }

    public function record(Traveler $traveler, string $event, string $key, string $message): Notification
    {
        $notification = Notification::firstOrCreate(['event_key' => $key], [
            'traveler_id' => $traveler->id, 'event' => $event, 'phone' => $traveler->phone,
            'message' => 'أهلًا '.$traveler->name."\n".$message."\nوقت التسجيل: ".now()->timezone('Africa/Cairo')->format('Y-m-d H:i'),
            'created_by' => auth('web')->id(), 'status' => $traveler->whatsapp_enabled ? 'pending' : 'skipped',
            'result_code' => $traveler->whatsapp_enabled ? null : 'disabled_by_customer',
        ]);
        if ($notification->wasRecentlyCreated && $notification->status === 'pending') {
            $id = $notification->id;
            $tenantId = tenant('id');
            DB::afterCommit(function () use ($id, $tenantId) {
                // The outbox remains durable if sending or even the status update fails.
                try {
                    if (tenancy()->initialized && tenant('id') === $tenantId) {
                        $this->deliver($id);
                    }
                } catch (\Throwable) {
                    Log::warning('Umrah notification deferred', ['notification_id' => $id, 'tenant_id' => $tenantId]);
                }
            });
        }

        return $notification;
    }

    public function deliver(int $id): void
    {
        if (! tenancy()->initialized || ! tenant()->isUmrah()) {
            return;
        }
        // Claim before HTTP; a process crash leaves "sending", never an unsafe automatic duplicate.
        $claimed = DB::transaction(function () use ($id) {
            $notification = Notification::whereKey($id)->lockForUpdate()->firstOrFail();
            if ($notification->status !== 'pending') {
                return null;
            }
            $traveler = $notification->traveler;
            if (! $traveler->whatsapp_enabled) {
                $notification->update(['status' => 'skipped', 'result_code' => 'disabled_by_customer']);

                return null;
            }
            $sender = User::on('central')->where('tenant_id', tenant('id'))->find($notification->created_by);
            if (! $sender?->whatsapp_api_token) {
                $notification->update(['status' => 'skipped', 'result_code' => 'missing_sender_token']);

                return null;
            }
            try {
                $util = PhoneNumberUtil::getInstance();
                $phone = $util->parse($notification->phone, 'EG');
                if (! $util->isValidNumber($phone)) {
                    throw new \RuntimeException;
                }
                $normalized = $util->format($phone, PhoneNumberFormat::E164);
            } catch (\Throwable) {
                $notification->update(['status' => 'skipped', 'result_code' => 'invalid_phone']);

                return null;
            }
            $notification->update(['status' => 'sending', 'attempts' => $notification->attempts + 1, 'attempted_at' => now()]);

            return [$notification, $sender->whatsapp_api_token, $normalized];
        });
        if (! $claimed) {
            return;
        }
        [$notification, $token, $phone] = $claimed;
        try {
            $response = Http::connectTimeout(3)->timeout(10)->withHeaders(['X-API-Token' => $token])
                ->post(rtrim(config('services.whatsapp_bot.base_url'), '/').'/api/external/messages/send', [
                    'phone' => $phone, 'message' => $notification->message,
                ]);
            $body = $response->json() ?? [];
            $code = (string) ($body['errorCode'] ?? $body['code'] ?? '');
            $unavailable = in_array($code, ['NUMBER_NOT_REGISTERED', 'NOT_ON_WHATSAPP'], true);
            $accepted = $response->successful() && ($body['success'] ?? true) !== false && ($body['sent'] ?? true) !== false;
            $notification->update([
                'status' => $unavailable ? 'unavailable' : ($accepted ? 'accepted' : ($response->serverError() ? 'unknown' : 'failed')),
                'result_code' => $unavailable ? 'not_on_whatsapp' : 'http_'.$response->status(),
                'provider_message_id' => is_scalar($body['messageId'] ?? null) ? substr((string) $body['messageId'], 0, 255) : null,
            ]);
        } catch (\Throwable) {
            $notification->update(['status' => 'unknown', 'result_code' => 'transport_outcome_unknown']);
        }
    }
}
