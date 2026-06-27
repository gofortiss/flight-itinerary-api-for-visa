# Safeonward API curl examples

These curl examples show how to call the Safeonward onward ticket API from any backend, CRM, booking portal, or travel agency workflow.

## Environment

```bash
export SAFEONWARD_API_KEY="sk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
export SAFEONWARD_API_BASE_URL="https://safeonward.com/api/v1"
```

## Check wallet balance

```bash
curl "$SAFEONWARD_API_BASE_URL/balance" \
  -H "Authorization: Bearer $SAFEONWARD_API_KEY" \
  -H "Accept: application/json"
```

## Create a test onward ticket order

Test mode returns a demo reservation PDF and does not consume credits.

```bash
curl -X POST "$SAFEONWARD_API_BASE_URL/orders" \
  -H "Authorization: Bearer $SAFEONWARD_API_KEY" \
  -H "Content-Type: application/json" \
  -H "Idempotency-Key: demo-order-001" \
  -d '{
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
  }'
```

## Poll order status

```bash
curl "$SAFEONWARD_API_BASE_URL/orders/pao_your_order_id" \
  -H "Authorization: Bearer $SAFEONWARD_API_KEY" \
  -H "Accept: application/json"
```

## Download the PDF

```bash
curl -L "$SAFEONWARD_API_BASE_URL/orders/pao_your_order_id/pdf" \
  -H "Authorization: Bearer $SAFEONWARD_API_KEY" \
  -o onward-ticket.pdf
```

## Cancel an order

```bash
curl -X POST "$SAFEONWARD_API_BASE_URL/orders/pao_your_order_id/cancel" \
  -H "Authorization: Bearer $SAFEONWARD_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"reason":"Customer no longer needs the reservation."}'
```
