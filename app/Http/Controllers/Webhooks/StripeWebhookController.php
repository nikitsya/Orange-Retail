<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderPaymentService;
use App\Services\StripeCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use RuntimeException;
use Stripe\Exception\SignatureVerificationException;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function __construct(
        private StripeCheckoutService $stripeCheckoutService,
        private OrderPaymentService $orderPaymentService
    ) {}

    public function __invoke(Request $request): Response
    {
        try {
            $event = $this->stripeCheckoutService->constructWebhookEvent(
                $request->getContent(),
                $request->header('Stripe-Signature')
            );
        } catch (RuntimeException|SignatureVerificationException|UnexpectedValueException) {
            return response('Invalid Stripe webhook payload.', 400);
        }

        $session = $event->data->object ?? null;
        $sessionId = is_string($session?->id ?? null) ? $session->id : null;
        $paymentIntentId = is_string($session?->payment_intent ?? null) ? $session->payment_intent : null;

        if (! $sessionId) {
            return response()->noContent();
        }

        if ($event->type === 'checkout.session.completed') {
            if (($session?->payment_status ?? null) === 'paid') {
                $this->orderPaymentService->markStripeSessionPaid($sessionId, $paymentIntentId);
            } else {
                $this->orderPaymentService->markStripeSessionProcessing($sessionId, $paymentIntentId);
            }
        }

        match ($event->type) {
            'checkout.session.async_payment_succeeded' => $this->orderPaymentService->markStripeSessionPaid(
                $sessionId,
                $paymentIntentId
            ),
            'checkout.session.async_payment_failed' => $this->orderPaymentService->cancelStripeSessionOrder(
                $sessionId,
                Order::PAYMENT_STATUS_FAILED
            ),
            'checkout.session.expired' => $this->orderPaymentService->cancelStripeSessionOrder(
                $sessionId,
                Order::PAYMENT_STATUS_EXPIRED
            ),
            default => null,
        };

        return response()->noContent();
    }
}
