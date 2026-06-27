import json
import os
import uuid
from pathlib import Path
from urllib import request


def load_env(path=".env"):
    env_path = Path(path)
    if not env_path.exists():
        return

    for line in env_path.read_text().splitlines():
        line = line.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue

        key, value = line.split("=", 1)
        os.environ.setdefault(key, value)


def safeonward_request(method, path, payload=None, headers=None):
    api_key = os.getenv("SAFEONWARD_API_KEY")
    if not api_key:
        raise RuntimeError("Missing SAFEONWARD_API_KEY. Copy .env.example to .env and add your API key.")

    base_url = os.getenv("SAFEONWARD_API_BASE_URL", "https://safeonward.com/api/v1").rstrip("/")
    body = None if payload is None else json.dumps(payload).encode("utf-8")

    req = request.Request(
        f"{base_url}{path}",
        data=body,
        method=method,
        headers={
            "Authorization": f"Bearer {api_key}",
            "Accept": "application/json",
            "Content-Type": "application/json",
            **(headers or {}),
        },
    )

    with request.urlopen(req, timeout=150) as response:
        return json.loads(response.read().decode("utf-8"))


load_env()

payload = {
    "test_mode": True,
    "from_iata": os.getenv("SAFEONWARD_FROM_IATA", "CDG"),
    "to_iata": os.getenv("SAFEONWARD_TO_IATA", "BKK"),
    "departure_date": os.getenv("SAFEONWARD_DEPARTURE_DATE", "2026-08-15"),
    "email": os.getenv("SAFEONWARD_CUSTOMER_EMAIL", "client@example.com"),
    "billing_country": "FR",
    "passengers": [
        {
            "gender": "male",
            "first_name": "Jean",
            "last_name": "Dupont",
        }
    ],
}

order = safeonward_request(
    "POST",
    "/orders",
    payload,
    {"Idempotency-Key": f"python-test-{uuid.uuid4()}"},
)

print(json.dumps(order, indent=2))
