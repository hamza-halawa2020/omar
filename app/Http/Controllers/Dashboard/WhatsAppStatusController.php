<?php

namespace App\Http\Controllers\Dashboard;

use App\Services\WhatsAppService;
use Illuminate\Routing\Controller as BaseController;

class WhatsAppStatusController extends BaseController
{
    public function __invoke(WhatsAppService $whatsAppService)
    {
        $status = $whatsAppService->getStatus();

        return response()->json([
            'status' => (bool) ($status['success'] ?? false),
            'message' => ($status['success'] ?? false)
                ? __('messages.whatsapp_status_loaded')
                : ($status['error'] ?? __('messages.something_went_wrong')),
            'data' => [
                'walletPoints' => $status['walletPoints'] ?? $status['remainingPoints'] ?? data_get($status, 'wallet.walletPoints'),
                'dailyLimit' => $status['dailyLimit'] ?? null,
                'sentToday' => $status['sentToday'] ?? null,
                'remainingDailyLimit' => $status['remainingDailyLimit'] ?? null,
            ],
        ], ($status['success'] ?? false) ? 200 : 422);
    }
}
