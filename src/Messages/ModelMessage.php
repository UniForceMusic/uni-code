<?php

namespace Src\Messages;

use Closure;
use GuzzleHttp\Client as GuzzleClient;
use OpenAI;
use OpenAI\Client;
use OpenAI\Responses\StreamResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Src\Author;

class ModelMessage implements MessageInterface
{
    protected Client $client;
    protected ?StreamResponse $streamedResponse = null;
    protected bool $hasFinishedStreaming = false;
    protected string $content = '';

    public function __construct(
        protected string $prompt,
        protected ?string $systemPrompt
    ) {
        $httpClient = new GuzzleClient([]);

        $this->client = OpenAI::factory()
            ->withApiKey('abcdefgh12345678')
            ->withBaseUri('http://localhost:1234/v1')
            ->withHttpClient($httpClient)
            ->withStreamHandler(fn(RequestInterface $request): ResponseInterface => $httpClient->send($request, ['stream' => true]))
            ->make();
    }

    public function getAuthor(): Author
    {
        return Author::Model;
    }

    public function getContent(Closure $draw): string
    {
        if (!$this->streamedResponse) {
            $this->streamedResponse = $this->client->chat()->createStreamed([
                'model' => 'granite-4.1-8b',
                'messages' => [
                    ['role' => 'user', 'content' => $this->prompt],
                ]
            ]);
        }

        if ($this->hasFinishedStreaming) {
            return $this->content;
        }

        $hasStreams = false;

        foreach ($this->streamedResponse as $generator) {
            $hasStreams = true;

            if (count($generator->choices) == 0) {
                continue;
            }

            $this->content .= $generator->choices[0]->delta->content;

            $draw();

            break;
        }

        $this->hasFinishedStreaming = !$hasStreams;

        return $this->content;
    }

    public function hasFinishedStreaming(): bool
    {
        return $this->hasFinishedStreaming;
    }
}
