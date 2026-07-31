<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaystackService
{
    public function __construct(
        private ?string $secretKey = null,
        private ?string $baseUrl = null,
    ) {
        $this->secretKey = $secretKey ?? config('services.paystack.secret_key');
        $this->baseUrl = rtrim($baseUrl ?? config('services.paystack.payment_url', 'https://api.paystack.co'), '/');
    }

    /**
     * Initialize a transaction and return Paystack's data payload
     * (authorization_url, access_code, reference).
     *
     * @param  array<string, mixed>  $metadata
     * @return array<string, mixed>
     */
    public function initialize(string $email, int $amountKobo, string $reference, string $callbackUrl, array $metadata = []): array
    {
        $response = Http::withToken($this->secretKey)
            ->acceptJson()
            ->post("{$this->baseUrl}/transaction/initialize", [
                'email' => $email,
                'amount' => $amountKobo,
                'currency' => 'NGN',
                'reference' => $reference,
                'callback_url' => $callbackUrl,
                'metadata' => $metadata,
            ]);

        $body = $response->json();

        if (! $response->successful() || ! ($body['status'] ?? false)) {
            throw new RuntimeException(
                'Paystack initialization failed: '.($body['message'] ?? $response->status())
            );
        }

        return $body['data'];
    }

    /**
     * Verify a transaction by reference. Returns true only when Paystack
     * confirms the charge succeeded.
     */
    public function verify(string $reference): bool
    {
        $response = Http::withToken($this->secretKey)
            ->acceptJson()
            ->get("{$this->baseUrl}/transaction/verify/".urlencode($reference));

        if (! $response->successful()) {
            return false;
        }

        $body = $response->json();

        return ($body['status'] ?? false) === true
            && ($body['data']['status'] ?? null) === 'success';
    }

    /**
     * Validate a webhook payload against the x-paystack-signature header
     * (HMAC-SHA512 of the raw body, keyed with the secret key).
     */
    public function isValidSignature(string $rawPayload, ?string $signature): bool
    {
        if (empty($signature) || empty($this->secretKey)) {
            return false;
        }

        $expected = hash_hmac('sha512', $rawPayload, $this->secretKey);

        return hash_equals($expected, $signature);
    }
}
