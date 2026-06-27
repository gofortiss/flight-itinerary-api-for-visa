import { mkdirSync, writeFileSync } from 'node:fs';
import { apiBaseUrl, apiKey, requireApiKey } from './config.js';

requireApiKey();

const orderId = process.argv[2];

if (!orderId) {
  throw new Error('Usage: npm run pdf -- pao_your_order_id');
}

const response = await fetch(`${apiBaseUrl}/orders/${orderId}/pdf`, {
  redirect: 'follow',
  headers: {
    Authorization: `Bearer ${apiKey}`,
    Accept: 'application/pdf,application/json',
  },
});

if (!response.ok) {
  const text = await response.text();
  throw new Error(`Could not download PDF (${response.status}): ${text}`);
}

mkdirSync('downloads', { recursive: true });

const bytes = Buffer.from(await response.arrayBuffer());
const filePath = `downloads/${orderId}.pdf`;

writeFileSync(filePath, bytes);
console.log(`Downloaded ${filePath}`);
