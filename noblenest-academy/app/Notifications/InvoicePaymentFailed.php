<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Phase 5 — dunning notification when Stripe reports an invoice failure.
 *
 * Fired from PaymentController::handleInvoiceFailed (Phase 4). Queued so
 * webhook handlers stay <500 ms and Stripe doesn't retry.
 *
 * Tone: helpful, not alarming. Asks the parent to update their card via the
 * Billing Portal; explains they have a 7-day grace period before access is
 * suspended (matches Stripe's default subscription retry schedule).
 */
class InvoicePaymentFailed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $invoiceId,
        public readonly int $attemptCount = 1,
        public readonly ?int $amountCents = null,
        public readonly string $currency = 'USD',
    ) {}

    public function via($notifiable): array
    {
        // Database channel for in-app + mail channel for email. Push (FCM/APN)
        // when Phase 5 push pipeline lands.
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $portalUrl = route('billing.portal');
        $amount = $this->amountCents !== null
            ? number_format($this->amountCents / 100, 2).' '.strtoupper($this->currency)
            : 'your subscription payment';

        return (new MailMessage)
            ->subject('Action needed: your Noble Nest Academy payment')
            ->greeting('Hi there,')
            ->line("We tried to charge {$amount} on your subscription and the payment didn't go through (attempt {$this->attemptCount} of 4).")
            ->line('To keep your child\'s learning uninterrupted, please update your card or pay this invoice. We\'ll try again automatically over the next few days.')
            ->action('Update payment method', $portalUrl)
            ->line('If you\'d like help, just reply to this email and our support team will pick it up.')
            ->salutation('Warmly, the Noble Nest team');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'invoice_payment_failed',
            'invoice_id' => $this->invoiceId,
            'attempt_count' => $this->attemptCount,
            'amount_cents' => $this->amountCents,
            'currency' => $this->currency,
            'billing_portal' => route('billing.portal'),
        ];
    }
}
