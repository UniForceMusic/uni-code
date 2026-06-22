<?php

namespace Src\Messages;

use Closure;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request;
use OpenAI;
use OpenAI\Client;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Src\Author;

class ModelMessage implements MessageInterface
{
    protected Client $client;
    protected GuzzleClient $httpClient;

    /** Raw PSR-7 response body stream for the in-flight completion. */
    protected ?StreamInterface $stream = null;

    /** Underlying PHP stream resource pulled out of $stream, set non-blocking. */
    protected $rawResource = null;

    /** Bytes read from the wire that haven't formed a complete SSE event yet. */
    protected string $pendingBuffer = '';

    /** Full assistant text accumulated so far. */
    protected string $content = '';

    protected bool $started = false;
    protected bool $finished = false;

    public function __construct(
        protected string $prompt,
        protected ?string $systemPrompt
    ) {
        $this->httpClient = new GuzzleClient([]);

        $this->client = OpenAI::factory()
            ->withApiKey('abcdefgh12345678')
            ->withBaseUri('http://localhost:1234/v1')
            ->withHttpClient($this->httpClient)
            ->withStreamHandler(fn(RequestInterface $request): ResponseInterface => $this->httpClient->send($request, [
                'stream' => true,
            ]))
            ->make();
    }

    public function getAuthor(): Author
    {
        return Author::Model;
    }

    public function getContent(Closure $draw): string
    {
        if ($this->finished) {
            return $this->content;
        }

        if (!$this->started) {
            $this->startStream();
            $this->started = true;
        }

        $this->pump($draw);

        return $this->content;
    }

    public function hasFinishedStreaming(): bool
    {
        return $this->finished;
    }

    /**
     * Fire off the chat completion request and grab the raw response body
     * stream without blocking the caller. We bypass OpenAI\Client's own
     * createStreamed() generator here, since iterating it blocks until a
     * full SSE chunk is available - not acceptable in a 60Hz tick loop.
     */
    protected function startStream(): void
    {
        $messages = [];

        if ($this->systemPrompt !== null) {
            $messages[] = ['role' => 'system', 'content' => $this->systemPrompt];
        }

        $messages[] = ['role' => 'user', 'content' => $this->prompt];

        $request = new Request(
            'POST',
            'http://localhost:1234/v1/chat/completions',
            [
                'Authorization' => 'Bearer abcdefgh12345678',
                'Content-Type' => 'application/json',
                'Accept' => 'text/event-stream',
            ],
            json_encode([
                'model' => 'local-model',
                'messages' => $messages,
                'stream' => true,
            ])
        );

        // async + stream:true so Guzzle doesn't block waiting for the body to finish.
        $promise = $this->httpClient->sendAsync($request, ['stream' => true]);

        $response = $promise->wait();

        $this->stream = $response->getBody();

        // Drop down to the underlying PHP resource so we can flip it
        // into non-blocking mode. GuzzleHttp\Psr7\Stream exposes this
        // via detach().
        $resource = $this->stream->detach();

        if (is_resource($resource)) {
            stream_set_blocking($resource, false);
            $this->rawResource = $resource;
        }
    }

    /**
     * Non-blocking read of whatever bytes are currently available on the
     * socket, parse any complete SSE "data: ..." frames out of them, and
     * call $draw() for each new piece of content.
     */
    protected function pump(Closure $draw): void
    {
        if ($this->rawResource === null) {
            return;
        }

        if (feof($this->rawResource)) {
            $this->closeStream();
            return;
        }

        $chunk = fread($this->rawResource, 8192);

        if ($chunk === false || $chunk === '') {
            // Nothing available right now - not an error, just no new data this tick.
            return;
        }

        $this->pendingBuffer .= $chunk;

        // SSE events are separated by a blank line ("\n\n").
        while (($pos = strpos($this->pendingBuffer, "\n\n")) !== false) {
            $event = substr($this->pendingBuffer, 0, $pos);
            $this->pendingBuffer = substr($this->pendingBuffer, $pos + 2);

            $this->handleEvent($event, $draw);

            if ($this->finished) {
                return;
            }
        }
    }

    protected function handleEvent(string $event, Closure $draw): void
    {
        foreach (explode("\n", $event) as $line) {
            $line = trim($line);

            if ($line === '' || !str_starts_with($line, 'data:')) {
                continue;
            }

            $data = trim(substr($line, 5));

            if ($data === '[DONE]') {
                $this->closeStream();
                return;
            }

            $decoded = json_decode($data, true);

            if (!is_array($decoded)) {
                continue;
            }

            $delta = $decoded['choices'][0]['delta']['content'] ?? null;

            if ($delta !== null && $delta !== '') {
                $this->content .= $delta;
                $draw($delta);
            }

            $finishReason = $decoded['choices'][0]['finish_reason'] ?? null;

            if ($finishReason !== null) {
                $this->closeStream();
                return;
            }
        }
    }

    protected function closeStream(): void
    {
        if (is_resource($this->rawResource)) {
            fclose($this->rawResource);
        }

        $this->rawResource = null;
        $this->finished = true;
    }
}
