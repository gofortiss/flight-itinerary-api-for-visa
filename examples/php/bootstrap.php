<?php

declare(strict_types=1);

function loadEnv(string $path = __DIR__.'/../../.env'): void
{
    if (! is_file($path)) {
        return;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);

        $_ENV[$key] ??= $value;
        $_SERVER[$key] ??= $value;
        putenv($key.'='.$value);
    }
}

function envValue(string $key, ?string $default = null): ?string
{
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    return $value === false || $value === null || $value === '' ? $default : (string) $value;
}

function safeonwardApiKey(): string
{
    $apiKey = envValue('SAFEONWARD_API_KEY');

    if ($apiKey === null) {
        throw new RuntimeException('Missing SAFEONWARD_API_KEY. Copy .env.example to .env and add your API key.');
    }

    return $apiKey;
}

function safeonwardBaseUrl(): string
{
    return rtrim(envValue('SAFEONWARD_API_BASE_URL', 'https://safeonward.com/api/v1') ?? '', '/');
}

function safeonwardRequest(string $method, string $path, ?array $payload = null, array $headers = []): array|string
{
    $url = safeonwardBaseUrl().$path;
    $curl = curl_init($url);

    $requestHeaders = array_merge([
        'Authorization: Bearer '.safeonwardApiKey(),
        'Accept: application/json',
        'Content-Type: application/json',
    ], $headers);

    curl_setopt_array($curl, [
        CURLOPT_CUSTOMREQUEST => strtoupper($method),
        CURLOPT_HTTPHEADER => $requestHeaders,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 150,
    ]);

    if ($payload !== null) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload, JSON_THROW_ON_ERROR));
    }

    $body = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE) ?: '';

    if ($body === false) {
        $error = curl_error($curl);
        curl_close($curl);

        throw new RuntimeException('cURL error: '.$error);
    }

    curl_close($curl);

    $decoded = str_contains($contentType, 'application/json')
        ? json_decode($body, true, 512, JSON_THROW_ON_ERROR)
        : $body;

    if ($status < 200 || $status >= 300) {
        $message = is_array($decoded)
            ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : $decoded;

        throw new RuntimeException("Safeonward API error {$status}: {$message}");
    }

    return $decoded;
}

function exampleOrderPayload(bool $testMode = true): array
{
    return [
        'test_mode' => $testMode,
        'from_iata' => envValue('SAFEONWARD_FROM_IATA', 'CDG'),
        'to_iata' => envValue('SAFEONWARD_TO_IATA', 'BKK'),
        'departure_date' => envValue('SAFEONWARD_DEPARTURE_DATE', '2026-08-15'),
        'email' => envValue('SAFEONWARD_CUSTOMER_EMAIL', 'client@example.com'),
        'billing_country' => 'FR',
        'passengers' => [
            [
                'gender' => 'male',
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
            ],
        ],
    ];
}

function printJson(array|string $value): void
{
    if (is_array($value)) {
        echo json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;

        return;
    }

    echo $value.PHP_EOL;
}

loadEnv();
