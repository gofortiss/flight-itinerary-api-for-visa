import { apiBaseUrl, apiKey, requireApiKey } from './config.js';

export async function safeonwardRequest(path, options = {}) {
  requireApiKey();

  const response = await fetch(`${apiBaseUrl}${path}`, {
    ...options,
    headers: {
      Authorization: `Bearer ${apiKey}`,
      Accept: 'application/json',
      'Content-Type': 'application/json',
      ...options.headers,
    },
  });

  const contentType = response.headers.get('content-type') || '';
  const body = contentType.includes('application/json')
    ? await response.json()
    : await response.text();

  if (!response.ok) {
    const message = typeof body === 'string' ? body : JSON.stringify(body, null, 2);
    throw new Error(`Safeonward API error ${response.status}: ${message}`);
  }

  return body;
}

export function printJson(value) {
  console.log(JSON.stringify(value, null, 2));
}
