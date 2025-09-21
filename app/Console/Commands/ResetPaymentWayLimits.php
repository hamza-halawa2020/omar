<?php

namespace App\Console\Commands;

use App\Models\PaymentWay;
use Illuminate\Console\Command;

class ResetPaymentWayLimits extends Command
{
    protected $signature = 'payment-ways:reset-limits';

    protected $description = 'Reset send and receive alert limits for all payment ways';

    public function handle()
    {
        PaymentWay::query()->update([
            'send_limit_alert' => 0,
            'receive_limit_alert' => 0,
        ]);

        $this->info('Payment way limits reset successfully.');

        return 0;
    }
}
