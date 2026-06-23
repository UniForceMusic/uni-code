<?php

namespace Src\Messages;

use Closure;
use OpenAI;
use OpenAI\Client;
use OpenAI\Responses\StreamResponse;
use Src\Author;

class ModelMessage implements MessageInterface
{
    protected ?StreamResponse $streamedResponse = null;
    protected bool $hasFinishedStreaming = false;
    protected string $content = '';

    public function __construct(
        protected Client $client,
        protected string $systemPrompt,
        protected string $prompt,
    ) {
    }

    public function getAuthor(): Author
    {
        return Author::Model;
    }

    public function getContent(Closure $draw): string
    {
        if (!$this->streamedResponse) {
            $this->streamedResponse = $this->client->chat()->createStreamed([
                // 'model' => 'granite-4.1-8b',
                'model' => 'qwen3.6-35b-a3b-mtp',
                'messages' => [
                    ['role' => 'system', 'content' => $this->systemPrompt],
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
