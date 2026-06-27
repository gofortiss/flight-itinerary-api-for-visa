<?php

declare(strict_types=1);

require __DIR__.'/bootstrap.php';

$order = safeonwardRequest(
    'POST',
    '/orders',
    exampleOrderPayload(testMode: false),
    ['Idempotency-Key: php-live-'.bin2hex(random_bytes(12))]
);

printJson($order);
