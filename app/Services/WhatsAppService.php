<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserWhatsAppMessageTemplate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Exception;

class WhatsAppService
{
    protected $baseUrl;

    public const TEMPLATE_KEYS = [
        'greeting',
        'send',
        'receive',
        'product',
        'installment',
        'association',
        'association_payout',
        'balance',
    ];

    public function __construct()
    {
        $this->baseUrl = config('services.whatsapp_bot.base_url', 'http://localhost:3000');
    }


    public function sendMessage(string $phone, string $message): ?array
    {
        $token = auth()->user()?->whatsapp_api_token;

        if (!$token) {
            return null;
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

        $user = auth()->user();
        $templates = $this->templatesForUser($user);
        $client = $client->fresh();
        $variables = [
            'name' => $client->name,
            'amount' => $amount,
            'product' => $context['product'] ?? '',
            'association' => $context['association'] ?? $context['association_payout'] ?? '',
            'balance' => $client->debt,
        ];

        $greeting = $this->renderTemplate($templates['greeting'], $variables);
        $action = $type === 'send'
            ? $this->renderTemplate($templates['send'], $variables)
            : $this->renderTemplate($templates['receive'], $variables);

        $reason = '';
        if (isset($context['product'])) {
            $reason = $this->renderTemplate($templates['product'], $variables);
        } elseif (isset($context['installment'])) {
            $reason = $this->renderTemplate($templates['installment'], $variables);
        } elseif (isset($context['association'])) {
            $reason = $this->renderTemplate($templates['association'], $variables);
        } elseif (isset($context['association_payout'])) {
            $reason = $this->renderTemplate($templates['association_payout'], $variables);
        }

        $balance = $this->renderTemplate($templates['balance'], $variables);

        $message = "{$greeting}\n{$action}{$reason}\n{$balance}";

        return $this->sendMessage($client->full_phone_number ?? $client->phone_number, $message);
    }

    public function defaultTemplates(): array
    {
        return [
            'greeting' => __('messages.whatsapp_msg_greeting', ['name' => ':name']),
            'send' => __('messages.whatsapp_msg_send', ['amount' => ':amount']),
            'receive' => __('messages.whatsapp_msg_receive', ['amount' => ':amount']),
            'product' => __('messages.whatsapp_msg_product', ['product' => ':product']),
            'installment' => __('messages.whatsapp_msg_installment'),
            'association' => __('messages.whatsapp_msg_association', ['association' => ':association']),
            'association_payout' => __('messages.whatsapp_msg_association_payout', ['association' => ':association']),
            'balance' => __('messages.whatsapp_msg_balance', ['balance' => ':balance']),
        ];
    }

    public function templatesForUser(?User $user): array
    {
        $defaults = $this->defaultTemplates();

        if (! $user) {
            return $defaults;
        }

        if (! Schema::connection('central')->hasTable('user_whatsapp_message_templates')) {
            return $defaults;
        }

        $templates = Cache::remember(
            $this->templatesCacheKey($user->id),
            now()->addDay(),
            fn () => UserWhatsAppMessageTemplate::query()
                ->where('user_id', $user->id)
                ->pluck('template', 'key')
                ->all()
        );

        return array_merge($defaults, array_filter($templates, fn ($value) => $value !== null && $value !== ''));
    }

    public function clearTemplatesCache(int $userId): void
    {
        Cache::forget($this->templatesCacheKey($userId));
    }

    private function templatesCacheKey(int $userId): string
    {
        return "user:{$userId}:whatsapp-message-templates";
    }

    private function renderTemplate(string $template, array $variables): string
    {
        $replace = [];

        foreach ($variables as $key => $value) {
            $replace[':'.$key] = (string) $value;
        }

        return strtr($template, $replace);
    }
}
