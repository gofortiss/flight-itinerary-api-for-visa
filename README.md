# Flight Itinerary API for Visa Applications

Node.js and curl examples for the **Safeonward flight itinerary API for visa applications**, also known as an onward ticket API, flight reservation API, dummy ticket API, or PNR generator API.

The official Safeonward B2B API helps approved agencies generate real verifiable flight PNRs and visa-ready flight reservation PDFs from their own CRM, booking portal, or agency workflow. No IATA accreditation is required.

> Official website: [safeonward.com](https://safeonward.com)  
> API landing page: [safeonward.com/onward-ticket/api](https://safeonward.com/onward-ticket/api)  
> API documentation: [safeonward.com/api/docs/public-b2b-v1](https://safeonward.com/api/docs/public-b2b-v1)

## What this repository contains

- A minimal Node.js client using native `fetch`
- Plain PHP examples using cURL for server-side agency integrations
- A Python example using the standard library
- A test mode order example that creates a demo onward ticket PDF without consuming credits
- A live order example for approved B2B agencies
- Wallet balance, order status, PDF download, and cancellation examples
- curl examples for teams that do not use Node.js
- SEO-friendly integration notes for travel agency API and visa workflow use cases

## Common use cases

Travel and visa businesses use the Safeonward API to build:

- Onward ticket API integrations for agency portals
- Flight reservation PDF automation for visa applications
- Proof of onward travel workflows for travelers
- Dummy ticket API demos in sandbox or test mode
- B2B prepaid wallet integrations for high-volume agencies
- Internal tools for visa consultants and immigration service desks

## Requirements

- Node.js 18 or newer
- A Safeonward B2B API key
- A prepaid agency wallet for live orders

You can request agency access from the Safeonward website:

[Request Safeonward B2B API access](https://safeonward.com/b2b/access-request)

## Quick start

Clone the repository:

```bash
git clone https://github.com/gofortiss/flight-itinerary-api-for-visa.git
cd flight-itinerary-api-for-visa
```

Create your environment file:

```bash
cp .env.example .env
```

Edit `.env`:

```env
SAFEONWARD_API_KEY=sk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
SAFEONWARD_API_BASE_URL=https://safeonward.com/api/v1
SAFEONWARD_FROM_IATA=CDG
SAFEONWARD_TO_IATA=BKK
SAFEONWARD_DEPARTURE_DATE=2026-08-15
SAFEONWARD_CUSTOMER_EMAIL=client@example.com
```

No Node dependencies are required. The JavaScript examples use native Node.js APIs.

## PHP examples

The PHP examples are useful for Laravel, Symfony, WordPress, custom agency portals, and any backend that can run PHP cURL.

```bash
php examples/php/check-balance.php
php examples/php/create-test-order.php
php examples/php/create-live-order.php
php examples/php/check-order-status.php pao_your_order_id
php examples/php/download-pdf.php pao_your_order_id
php examples/php/cancel-order.php pao_your_order_id
```

Full PHP guide:

[docs/php-examples.md](docs/php-examples.md)

## Python example

```bash
python3 examples/python/create_test_order.py
```

## Node.js examples

### Check wallet balance

```bash
npm run balance
```

Response:

```json
{
  "total_credits": 20,
  "used_credits": 3,
  "remaining_credits": 17
}
```

### Create a test onward ticket order

Test mode is the safest way to verify your integration. It returns a demo reservation with a signed PDF URL, does not contact the airline provider, and does not consume wallet credits.

```bash
npm run create:test
```

The script sends:

```json
{
  "test_mode": true,
  "from_iata": "CDG",
  "to_iata": "BKK",
  "departure_date": "2026-08-15",
  "email": "client@example.com",
  "billing_country": "FR",
  "passengers": [
    {
      "gender": "male",
      "first_name": "Jean",
      "last_name": "Dupont"
    }
  ]
}
```

Example ready response:

```json
{
  "order_id": "pao_abc123",
  "status": "ready",
  "service": "onward_ticket",
  "test_mode": true,
  "booking_reference": "TESTPNR",
  "pdf_url": "https://safeonward.com/order/reservation/1/download?expires=...",
  "status_url": "https://safeonward.com/api/v1/orders/pao_abc123",
  "credits_remaining": 20,
  "credit_refunded": false
}
```

### Create a live order

Live orders consume one wallet credit and attempt to create a real reservation PDF.

```bash
npm run create:live
```

The API may return `ready`, `pending`, or `failed`.

#### Ready

`ready` means the reservation PDF is available immediately.

```json
{
  "order_id": "pao_abc123",
  "status": "ready",
  "booking_reference": "PNR123",
  "pdf_url": "https://safeonward.com/order/reservation/1/download?expires=...",
  "credits_remaining": 19
}
```

#### Pending

`pending` means fulfillment is still running. Poll the `status_url`.

```json
{
  "order_id": "pao_abc123",
  "status": "pending",
  "status_url": "https://safeonward.com/api/v1/orders/pao_abc123",
  "retry_after": 5,
  "credits_remaining": 19
}
```

#### Failed

If no valid reservation PDF is produced, Safeonward refunds the credit automatically.

```json
{
  "order_id": "pao_abc123",
  "status": "failed",
  "credit_refunded": true,
  "error": {
    "code": "provider_draft_failed",
    "message": "Provider refused the reservation."
  }
}
```

### Poll order status

```bash
npm run status -- pao_your_order_id
```

### Download the onward ticket PDF

```bash
npm run pdf -- pao_your_order_id
```

The file is saved to:

```text
downloads/pao_your_order_id.pdf
```

## Cancel an order

Use the cancel endpoint when your customer no longer needs the reservation.

```bash
curl -X POST "https://safeonward.com/api/v1/orders/pao_your_order_id/cancel" \
  -H "Authorization: Bearer $SAFEONWARD_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"reason":"Customer no longer needs the reservation."}'
```

Cancellation is idempotent. Calling it again for the same order returns the canceled order and does not refund another credit.

## API endpoints

| Method | Endpoint | Purpose |
| --- | --- | --- |
| `GET` | `/api/v1/balance` | Check wallet credits |
| `POST` | `/api/v1/orders` | Create an onward ticket reservation |
| `GET` | `/api/v1/orders/{order_id}` | Check order status |
| `GET` | `/api/v1/orders/{order_id}/pdf` | Redirect to a signed PDF download |
| `POST` | `/api/v1/orders/{order_id}/cancel` | Cancel an order and release/refund when possible |
| `POST` | `/api/v1/wallet/reloads` | Create a Stripe Checkout wallet reload |

## Required order fields

| Field | Type | Notes |
| --- | --- | --- |
| `from_iata` | string | 3-letter departure airport code |
| `to_iata` | string | 3-letter arrival airport code, different from `from_iata` |
| `departure_date` | date | At least 10 days from today |
| `email` | string | Customer email |
| `passengers` | array | 1 to 11 passengers |
| `passengers.*.gender` | string | `male` or `female` |
| `passengers.*.first_name` | string | Minimum 2 characters |
| `passengers.*.last_name` | string | Minimum 2 characters |

## Important integration notes

- Always send an `Idempotency-Key` when creating an order.
- Test mode does not consume credits.
- Live mode consumes one credit when the order is accepted.
- If fulfillment fails before a valid PDF is produced, the credit is refunded.
- If the API returns `pending`, poll the `status_url` after `retry_after` seconds.
- Do not expose your API key in frontend JavaScript, mobile apps, GitHub issues, or public logs.
- Downloaded PDFs are delivered through temporary signed URLs.

## Example backend flow

1. Your CRM receives customer route and passenger details.
2. Your backend calls `POST /api/v1/orders` with an idempotency key.
3. If the response is `ready`, show or email the `pdf_url`.
4. If the response is `pending`, store the `order_id` and poll the `status_url`.
5. If the response is `failed`, show the error and rely on the automatic credit refund.
6. If the customer cancels, call `POST /api/v1/orders/{order_id}/cancel`.

## SEO integration phrases

This repository is a practical example for developers searching for:

- onward ticket API
- onward flight ticket API
- flight reservation API
- flight reservation PDF API
- dummy ticket API for visa applications
- PNR generator API
- proof of onward travel API
- visa travel agency API
- B2B travel agency API

## Links

- Website: [https://safeonward.com](https://safeonward.com)
- API page: [https://safeonward.com/onward-ticket/api](https://safeonward.com/onward-ticket/api)
- API docs: [https://safeonward.com/api/docs/public-b2b-v1](https://safeonward.com/api/docs/public-b2b-v1)
- Request access: [https://safeonward.com/b2b/access-request](https://safeonward.com/b2b/access-request)

## License

MIT
