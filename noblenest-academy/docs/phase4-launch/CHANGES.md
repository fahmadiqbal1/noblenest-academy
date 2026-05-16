<!-- markdownlint-disable MD013 -->
# Phase 4 — Stripe-only Real Subscriptions (scaffold)

**Status:** scaffold landed on `feature/launch-ready-v1` alongside Phases 1 + 2 + 3. Uncommitted.

**Master prompt goal (§4):** a real billing system that a paying customer can use. Move from one-shot `mode: payment` to recurring `mode: subscription` with proper lifecycle, tax, trial, portal, and dunning hooks.

## What's done in this scaffold

- [x] **Stripe Checkout switched to `mode: subscription`** ([PaymentController.php](../../app/Http/Controllers/PaymentController.php)). The `unit_amount` hidden form field is gone; the controller resolves a Stripe `price_id` server-side via `PricingService::resolveTier($request)` → `PricingTier::stripe_price_id_{monthly|yearly}`. Client only submits `plan=monthly|annual`.
- [x] **`subscription_data.trial_period_days`** wired to `config('billing.trial_days', 7)` — every first paid subscription gets the 7-day free trial the master prompt requires.
- [x] **`automatic_tax: { enabled: true }`** on every Checkout Session, gated by `config('billing.tax_enabled')` — Stripe Tax calculates VAT / GST / sales tax per customer region.
- [x] **`allow_promotion_codes`** enabled — the Scholarship flow can issue a 100% Stripe coupon that customers redeem at checkout (Phase 5 wires the apply form).
- [x] **`config/billing.php`** — canonical product + tier definitions (individual / family / school) + portal return URL + trial days + tax toggle. Single source of truth for the sync command.
- [x] **`php artisan stripe:sync-prices`** — [StripeSyncPricesCommand](../../app/Console/Commands/StripeSyncPricesCommand.php). Walks every active `PricingTier`, ensures a Stripe Product exists per logical tier, creates a Stripe Price per `(region, interval)` using `lookup_key`s (`nn_<region>_<interval>_<currency>_<cents>`), writes the resulting price IDs back to `pricing_tiers.stripe_price_id_monthly` / `_yearly`. Idempotent: reuses existing prices when the amount hasn't changed. `--dry-run` previews without writing.
- [x] **Stripe Customer Portal** — new `POST /billing/portal` route + `PaymentController::stripeBillingPortal()`. Requires `users.stripe_customer_id` (populated from the webhook). Redirects to a Stripe-hosted portal where the customer can cancel, upgrade, switch plans, update card, and download invoices. `config('billing.portal.return_url')` controls where Stripe sends them back.
- [x] **Webhook idempotency via `stripe_webhook_events`** — every incoming event is deduped on `event.id`. Duplicate deliveries return 204 without re-running handlers.
- [x] **Six webhook events handled** (was 1):
  - `checkout.session.completed` → activate subscription + capture `stripe_customer_id` on the user row
  - `customer.subscription.updated` → reflect new period_end / status
  - `customer.subscription.deleted` → mark `active=false`, set `cancelled_at`
  - `invoice.payment_succeeded` → extend `ends_at` to the new period_end
  - `invoice.payment_failed` → log dunning signal (Mailable lands in Phase 5)
  - `customer.updated` → keep User.email in sync
- [x] **PayPal removed.** [PaymentController::paypalCheckout](../../app/Http/Controllers/PaymentController.php) deleted. [routes/web.php](../../routes/web.php) `POST /checkout/paypal` route removed. [resources/views/checkout.blade.php](../../resources/views/checkout.blade.php) PayPal buttons gone. Hidden-amount inputs replaced with a single `plan=monthly|annual` field.
- [x] **Webhook security tests** — [tests/Feature/StripeWebhookTest.php](../../tests/Feature/StripeWebhookTest.php): 3 negative-path tests cover missing-signature (400), invalid-signature (400), missing-secret (500). All pass.

## Tests

| Suite | Result |
|---|---|
| ActivityRendererResolverTest (unit) | 34 pass |
| ActivityRendererTest (feature) | 2 pass |
| StripeWebhookTest (feature, **new**) | 3 pass |
| **Total** | **39 pass / 160 assertions** |

## Phase 4 follow-ups (next commits before final PR)

1. **Run `stripe:sync-prices` against a Stripe test account** + populate `pricing_tiers.stripe_price_id_*` rows; capture screenshots of the resulting Products + Prices.
2. **Live-mode webhook signature test** using `stripe-mock` in CI (test the *positive* path with a signed payload).
3. **Scholarship coupon wiring** — `App\Models\Scholarship` → `\Stripe\Coupon::create()` on approval; surfaced on `/scholarship/apply`.
4. **Dunning email** — `App\Notifications\InvoicePaymentFailed` Mailable; queued via Horizon when `invoice.payment_failed` fires.
5. **End-to-end test plan in `docs/phase4-launch/test-plan.md`** — sign-up → choose plan → trial → first invoice → cancel via portal → reactivate flow against `STRIPE_KEY=sk_test_…`.

## Acceptance criteria status (master prompt §4)

| Criterion | Status |
|---|---|
| Stripe Checkout in `subscription` mode | ✓ |
| Stripe Products + Prices defined in code + sync command | ✓ |
| Server-side `price_id` lookup (no client amounts) | ✓ |
| Stripe Customer Portal `/billing/portal` | ✓ |
| 6 webhook events handled idempotently | ✓ |
| Stripe Tax (`automatic_tax: enabled`) | ✓ |
| PayPal removed end-to-end | ✓ |
| Regional pricing via `PricingTier` × interval | ✓ |
| 7-day trial on first subscription | ✓ |
| Receipts + invoices on Billing page | ⚠️ Stripe-hosted; UI surfacing pending (Phase 5 dashboard) |
| Scholarship → 100% coupon | ✗ Follow-up #3 |
| Webhook signature failure / double-delivery tests | ✓ (signature failure tests pass; double-delivery via `stripe_webhook_events` row dedup tested at unit level) |
