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

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('WhatsApp message failed', ['status' => $response->status(), 'body' => $response->body(), 'phone' => $phone]);

            return null;
        } catch (Exception $e) {
            Log::error('WhatsApp message exception', ['error' => $e->getMessage(), 'phone' => $phone]);

            throw $e;
        }
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

        try {
            $this->sendMessage($client->phone_number, $message);
        } catch (Exception $e) {
            Log::error('Failed to send WhatsApp message to client: ' . $e->getMessage());
        }
    }
}
