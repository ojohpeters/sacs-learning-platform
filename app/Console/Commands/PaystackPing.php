<?php

namespace App\Console\Commands;

use App\Services\PaystackService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class PaystackPing extends Command
{
    protected $signature = 'paystack:ping';

    protected $description = 'Verify Paystack credentials by initializing a throwaway test transaction';

    public function handle(PaystackService $paystack): int
    {
        if (config('services.paystack.fake')) {
            $this->warn('PAYSTACK_FAKE=true — checkout is bypassing Paystack. Set it to false for the real gateway.');
        }

        if (empty(config('services.paystack.secret_key'))) {
            $this->error('✗ PAYSTACK_SECRET_KEY is not set. Add it to your .env and run `php artisan config:clear`.');

            return self::FAILURE;
        }

        try {
            $data = $paystack->initialize(
                email: 'ping@example.com',
                amountKobo: 5000, // ₦50
                reference: 'PING-' . strtoupper(Str::random(8)),
                callbackUrl: url('/payment/callback'),
                metadata: ['ping' => true],
            );

            $this->info('✓ Paystack credentials are valid.');
            $this->line('  authorization_url: ' . ($data['authorization_url'] ?? '(none)'));

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('✗ Paystack call failed: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
