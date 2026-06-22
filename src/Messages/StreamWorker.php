<?php

declare(strict_types=1);

use OpenAI\Client;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

if (count($argv) < 7) {
    exit(1);
}

$apiKey = $argv[1];
$baseUri = rtrim($argv[2], '/');
$model = $argv[3];
$prompt = $argv[4];
$tempFile = $argv[5];

file_put_contents($tempFile, '', LOCK_EX);

$httpClient = new \GuzzleHttp\Client([
    'headers' => [
        'Authorization' => "Bearer {$apiKey}",
        'Content-Type' => 'application/json',
        'OpenAI-Beta' => 'responses=v1',
    ],
]);

$streamHandler = fn(RequestInterface $request): ResponseInterface => $httpClient->send($request, ['stream' => true]);

$client = \OpenAI::factory()
    ->withApiKey('unused')
    ->withBaseUri($baseUri)
    ->withHttpClient($httpClient)
    ->withStreamHandler($streamHandler)
    ->make();

$stream = $client->responses()->createStreamed([
    'model' => $model,
    'input' => $prompt,
]);

foreach ($stream as $response) {
    $delta = '';

    match ($response->event) {
        'response.output_text.delta' => $delta = $response->response->delta,
        'response.reasoning_text.delta' => $delta = $response->response->delta,
        default => null,
    };

    if ($delta !== '') {
        file_put_contents($tempFile, $delta, FILE_APPEND | LOCK_EX);
    }
}
