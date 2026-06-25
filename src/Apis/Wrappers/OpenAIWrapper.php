<?php

namespace Src\Apis\Wrappers;

use BackedEnum;
use OpenAI;
use OpenAI\Client;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Src\Apis\Streams\OpenAIStream;
use Src\Apis\Streams\Type;
use Src\Messages\MessageInterface;

class OpenAIWrapper implements WrapperInterface
{
    protected Client $client;

    public function __construct(
        string $baseUri,
        string $apiKey
    ) {
        $this->client = OpenAI::factory()
            ->withBaseUri($baseUri)
            ->withApiKey($apiKey)
            ->withHttpClient($httpClient = new \GuzzleHttp\Client([]))
            ->withStreamHandler(fn(RequestInterface $request): ResponseInterface => $httpClient->send($request, ['stream' => true]))
            ->make();
    }

    public function prompt(
        string|BackedEnum $model,
        string $prompt,
        /** @var MessageInterface[] */
        array $previousMessages = []
    ): OpenAIStream {
        $messages = [];

        foreach ($previousMessages as $previousMessage) {
            $type = $previousMessage->getType();

            if ($type === Type::ToolCall) {
                continue;
            }

            $messages[] = [
                'role' => $type->value,
                'content' => $previousMessage->getContent()
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $prompt
        ];

        $streamResponse = $this->client->chat()->createStreamed([
            'model' => $model,
            'messages' => $messages
        ]);

        return new OpenAIStream($streamResponse);
    }
}
