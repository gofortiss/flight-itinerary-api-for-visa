import { printJson, safeonwardRequest } from './safeonward-client.js';

const orderId = process.argv[2];

if (!orderId) {
  throw new Error('Usage: npm run status -- pao_your_order_id');
}

const order = await safeonwardRequest(`/orders/${orderId}`);

printJson(order);
