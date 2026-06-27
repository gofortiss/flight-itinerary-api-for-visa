import { existsSync, readFileSync } from 'node:fs';

if (existsSync('.env')) {
  const lines = readFileSync('.env', 'utf8').split(/\r?\n/);

  for (const line of lines) {
    const trimmed = line.trim();

    if (!trimmed || trimmed.startsWith('#') || !trimmed.includes('=')) {
      continue;
    }

    const [key, ...valueParts] = trimmed.split('=');
    process.env[key] ??= valueParts.join('=');
  }
}

export const apiBaseUrl = process.env.SAFEONWARD_API_BASE_URL || 'https://safeonward.com/api/v1';
export const apiKey = process.env.SAFEONWARD_API_KEY || '';

export function requireApiKey() {
  if (!apiKey) {
    throw new Error('Missing SAFEONWARD_API_KEY. Copy .env.example to .env and add your API key.');
  }
}

export function examplePassenger() {
  return {
    gender: 'male',
    first_name: 'Jean',
    last_name: 'Dupont',
  };
}

export function exampleOrderPayload({ testMode = true } = {}) {
  return {
    test_mode: testMode,
    from_iata: process.env.SAFEONWARD_FROM_IATA || 'CDG',
    to_iata: process.env.SAFEONWARD_TO_IATA || 'BKK',
    departure_date: process.env.SAFEONWARD_DEPARTURE_DATE || '2026-08-15',
    email: process.env.SAFEONWARD_CUSTOMER_EMAIL || 'client@example.com',
    billing_country: 'FR',
    passengers: [examplePassenger()],
  };
}
