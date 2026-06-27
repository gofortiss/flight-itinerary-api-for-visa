<?php

declare(strict_types=1);

require __DIR__.'/bootstrap.php';

$orderId = $argv[1] ?? null;

if ($orderId === null) {
    throw new InvalidArgumentException('Usage: php examples/php/cancel-order.php pao_your_order_id');
}

$order = safeonwardRequest('POST', '/orders/'.rawurlencode($orderId).'/cancel', [
    'reason' => 'Customer no longer needs the reservation.',
]);

printJson($order);
