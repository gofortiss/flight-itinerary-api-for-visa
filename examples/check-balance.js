import { printJson, safeonwardRequest } from './safeonward-client.js';

const balance = await safeonwardRequest('/balance');

printJson(balance);
