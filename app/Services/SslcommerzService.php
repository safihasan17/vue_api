<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SslcommerzService
{
    private function baseUrl(): string
    {
        return config('sslcommerz.sandbox')
            ? 'https://sandbox.sslcommerz.com'
            : 'https://securepay.sslcommerz.com';
    }

    private function credentials(): array
    {
        return [
            'store_id' => config('sslcommerz.store_id'),
            'store_passwd' => config('sslcommerz.store_password'),
        ];
    }

    public function isConfigured(): bool
    {
        return filled(config('sslcommerz.store_id')) && filled(config('sslcommerz.store_password'));
    }

    /**
     * Start a payment session. Returns the gateway response
     * (status = SUCCESS and GatewayPageURL on success).
     */
    public function createSession(array $payload): array
    {
        $response = Http::asForm()
            ->timeout(30)
            ->post($this->baseUrl() . '/gwprocess/v4/api.php', array_merge($this->credentials(), $payload));

        return $response->json() ?? [];
    }

    /**
     * Ask SSLCommerz's server whether a val_id is a real, successful payment.
     * Never trust the browser redirect alone.
     */
    public function validate(string $valId): array
    {
        $response = Http::timeout(30)->get($this->baseUrl() . '/validator/api/validationserverAPI.php', array_merge(
            $this->credentials(),
            ['val_id' => $valId, 'format' => 'json']
        ));

        return $response->json() ?? [];
    }
}
