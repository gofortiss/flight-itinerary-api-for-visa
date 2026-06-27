<?php

declare(strict_types=1);

require __DIR__.'/bootstrap.php';

$order = safeonwardRequest(
    'POST',
    '/orders',
    exampleOrderPayload(testMode: true),
    ['Idempotency-Key: php-test-'.bin2hex(random_bytes(12))]
);

printJson($order);
