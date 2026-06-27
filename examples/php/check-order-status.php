<?php

declare(strict_types=1);

require __DIR__.'/bootstrap.php';

$orderId = $argv[1] ?? null;

if ($orderId === null) {
    throw new InvalidArgumentException('Usage: php examples/php/check-order-status.php pao_your_order_id');
}

$order = safeonwardRequest('GET', '/orders/'.rawurlencode($orderId));

printJson($order);
