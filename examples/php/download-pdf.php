<?php

declare(strict_types=1);

require __DIR__.'/bootstrap.php';

$orderId = $argv[1] ?? null;

if ($orderId === null) {
    throw new InvalidArgumentException('Usage: php examples/php/download-pdf.php pao_your_order_id');
}

$url = safeonwardBaseUrl().'/orders/'.rawurlencode($orderId).'/pdf';
$curl = curl_init($url);

curl_setopt_array($curl, [
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer '.safeonwardApiKey(),
        'Accept: application/pdf,application/json',
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 150,
]);

$body = curl_exec($curl);
$status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

if ($body === false) {
    $error = curl_error($curl);
    curl_close($curl);

    throw new RuntimeException('cURL error: '.$error);
}

curl_close($curl);

if ($status < 200 || $status >= 300) {
    throw new RuntimeException("Could not download PDF ({$status}): {$body}");
}

if (! is_dir(__DIR__.'/../../downloads')) {
    mkdir(__DIR__.'/../../downloads', 0755, true);
}

$filePath = __DIR__.'/../../downloads/'.$orderId.'.pdf';
file_put_contents($filePath, $body);

echo 'Downloaded downloads/'.$orderId.'.pdf'.PHP_EOL;
