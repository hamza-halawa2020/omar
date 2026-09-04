<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.whatsapp_bot.base_url', 'http://localhost:3000');
    }


    public function sendMessage(string $phone, string $message): ?array
    {
        $token = auth()->user()->whatsapp_api_token;

        if (!$token) {
            throw new Exception("WhatsApp API token is not configured for this user.");
        }

        try {
            $response = Http::withHeaders([
                'X-API-Token' => $token,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/api/external/messages/send', ['phone' => $phone, 'message' => $message]);

            $body = $response->json() ?? [];
            $body = $this->withWalletBalance($token, $body);

            if ($response->successful()) {
                return array_merge([
                    'success' => true,
                    'sent' => true,
                    'status_code' => $response->status(),
                ], $body);
            }

            Log::error('WhatsApp message failed', ['status' => $response->status(), 'body' => $response->body(), 'phone' => $phone]);

            return array_merge([
                'success' => false,
                'sent' => false,
                'status_code' => $response->status(),
                'error' => $body['error'] ?? 'WhatsApp message failed',
            ], $body);
        } catch (Exception $e) {
            Log::error('WhatsApp message exception', ['error' => $e->getMessage(), 'phone' => $phone]);

            return [
                'success' => false,
                'sent' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getStatus(): array
    {
        $token = auth()->user()->whatsapp_api_token;

        if (!$token) {
            return [
                'success' => false,
                'error' => __('messages.whatsapp_api_token_missing'),
            ];
        }

        try {
            $response = Http::withHeaders([
                'X-API-Token' => $token,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/api/external/wallet');

            $body = $response->json() ?? [];

            return array_merge([
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'error' => $body['error'] ?? null,
            ], $body);
        } catch (Exception $e) {
            Log::warning('WhatsApp status lookup failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function withWalletBalance(string $token, array $body): array
    {
        if (array_key_exists('remainingPoints', $body)) {
            return $body;
        }

        try {
            $walletResponse = Http::withHeaders([
                'X-API-Token' => $token,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/api/external/wallet');

            if (! $walletResponse->successful()) {
                return $body;
            }

            $walletBody = $walletResponse->json() ?? [];
            $points = $walletBody['remainingPoints']
                ?? $walletBody['walletPoints']
                ?? data_get($walletBody, 'wallet.walletPoints');

            if ($points !== null) {
                $body['remainingPoints'] = $points;
            }
        } catch (Exception $e) {
            Log::warning('WhatsApp wallet balance lookup failed', ['error' => $e->getMessage()]);
        }

        return $body;
    }

    public function sendTransactionMessage($client, $amount, $type, $context = [])
    {
        if (!$client || empty($client->phone_number)) {
            return;
        }

        $greeting = __('messages.whatsapp_msg_greeting', ['name' => $client->name]);
        $action = $type === 'send' ? __('messages.whatsapp_msg_send', ['amount' => $amount]) : __('messages.whatsapp_msg_receive', ['amount' => $amount]);

        $reason = '';
        if (isset($context['product'])) {
            $reason = __('messages.whatsapp_msg_product', ['product' => $context['product']]);
        } elseif (isset($context['installment'])) {
            $reason = __('messages.whatsapp_msg_installment');
        } elseif (isset($context['association'])) {
            $reason = __('messages.whatsapp_msg_association', ['association' => $context['association']]);
        } elseif (isset($context['association_payout'])) {
            $reason = __('messages.whatsapp_msg_association_payout', ['association' => $context['association_payout']]);
        }

        $balance = __('messages.whatsapp_msg_balance', ['balance' => $client->fresh()->debt]);

        $message = "{$greeting}\n{$action}{$reason}\n{$balance}";

        return $this->sendMessage($client->full_phone_number ?? $client->phone_number, $message);
    }
}
