# PHP flight itinerary API examples

These examples show how to call the Safeonward flight itinerary API for visa applications from a plain PHP backend.

They use PHP cURL only. No Composer package is required.

## Requirements

- PHP 8.1 or newer
- PHP cURL extension
- A Safeonward B2B API key

## Setup

```bash
cp .env.example .env
```

Edit `.env`:

```env
SAFEONWARD_API_KEY=sk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
SAFEONWARD_API_BASE_URL=https://safeonward.com/api/v1
```

## Check agency wallet balance

```bash
php examples/php/check-balance.php
```

## Create a test flight itinerary order

Test mode creates a demo flight reservation PDF and does not consume credits.

```bash
php examples/php/create-test-order.php
```

## Create a live flight itinerary order

Live mode consumes one prepaid wallet credit.

```bash
php examples/php/create-live-order.php
```

## Poll order status

```bash
php examples/php/check-order-status.php pao_your_order_id
```

## Download the reservation PDF

```bash
php examples/php/download-pdf.php pao_your_order_id
```

The PDF is saved in:

```text
downloads/pao_your_order_id.pdf
```

## Cancel an order

```bash
php examples/php/cancel-order.php pao_your_order_id
```

## PHP integration notes

- Keep your API key on the server. Do not expose it in browser JavaScript.
- Always send an `Idempotency-Key` when creating an order.
- Use test mode before live orders.
- If the API returns `pending`, store the `order_id` and poll the status endpoint.
- If fulfillment fails before a valid PDF is produced, Safeonward refunds the credit automatically.
