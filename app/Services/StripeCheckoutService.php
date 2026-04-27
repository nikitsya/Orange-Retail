<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Carbon;
use RuntimeException;
use Stripe\Checkout\Session;
use Stripe\Event;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeCheckoutService
{
    /**
     * @return array{id: string, client_secret: string|null, payment_intent: string|null, expires_at: \Illuminate\Support\Carbon|null}
     */
    public function createSession(Order $order): array
    {
        $order->loadMissing('items');

        $session = $this->client()->checkout->sessions->create([
            'mode' => 'payment',
            'ui_mode' => 'custom',
            'customer_email' => $order->customer_email,
            'line_items' => $order->items
                ->map(fn ($item): array => [
                    'price_data' => [
                        'currency' => $this->currency(),
                        'product_data' => [
                            'name' => $item->product_name,
                            'metadata' => [
                                'product_id' => (string) $item->product_id,
                                'sku' => $item->product_sku,
                            ],
                        ],
                        'unit_amount' => $this->minorUnits($item->unit_price),
                    ],
                    'quantity' => $item->quantity,
                ])
                ->values()
                ->all(),
            'metadata' => $this->metadata($order),
            'payment_intent_data' => [
                'metadata' => $this->metadata($order),
            ],
            'return_url' => route('checkout.return', ['order' => $order]).'?session_id={CHECKOUT_SESSION_ID}',
        ]);

        return $this->normaliseSession($session);
    }

    /**
     * @return array{id: string, status: string|null, payment_status: string|null, payment_intent: string|null}
     */
    public function retrieveSession(string $sessionId): array
    {
        $session = $this->client()->checkout->sessions->retrieve($sessionId);

        return $this->normaliseSession($session);
    }

    public function constructWebhookEvent(string $payload, ?string $signature): Event
    {
        $webhookSecret = config('services.stripe.webhook_secret');

        if ($webhookSecret) {
            return Webhook::constructEvent($payload, $signature ?? '', $webhookSecret);
        }

        if (! app()->environment('testing')) {
            throw new RuntimeException('Stripe webhook secret is not configured.');
        }

        return Event::constructFrom(json_decode($payload, true) ?: []);
    }

    protected function client(): StripeClient
    {
        $secretKey = config('services.stripe.secret');

        if (! $secretKey) {
            throw new RuntimeException('Stripe secret key is not configured.');
        }

        return new StripeClient([
            'api_key' => $secretKey,
        ]);
    }

    /**
     * @return array<string, string>
     */
    protected function metadata(Order $order): array
    {
        return [
            'order_id' => (string) $order->id,
            'order_number' => $order->order_number,
            'user_id' => (string) $order->user_id,
        ];
    }

    protected function currency(): string
    {
        return strtolower((string) config('services.stripe.currency', 'eur'));
    }

    protected function minorUnits(string|float|int $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    /**
     * @return array{id: string, client_secret: string|null, status: string|null, payment_status: string|null, payment_intent: string|null, expires_at: \Illuminate\Support\Carbon|null}
     */
    protected function normaliseSession(Session $session): array
    {
        return [
            'id' => $session->id,
            'client_secret' => $session->client_secret,
            'status' => $session->status,
            'payment_status' => $session->payment_status,
            'payment_intent' => is_string($session->payment_intent) ? $session->payment_intent : null,
            'expires_at' => $session->expires_at ? Carbon::createFromTimestamp($session->expires_at) : null,
        ];
    }
}
