import { randomUUID } from 'node:crypto';
import { exampleOrderPayload } from './config.js';
import { printJson, safeonwardRequest } from './safeonward-client.js';

const order = await safeonwardRequest('/orders', {
  method: 'POST',
  headers: {
    'Idempotency-Key': `demo-test-${randomUUID()}`,
  },
  body: JSON.stringify(exampleOrderPayload({ testMode: true })),
});

printJson(order);
