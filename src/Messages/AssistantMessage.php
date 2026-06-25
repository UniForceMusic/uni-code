<?php

namespace Src\Messages;

use Closure;
use Src\Apis\Streams\StreamInterface;
use Src\Apis\Wrappers\WrapperInterface;

class AssistantMessage implements MessageInterface
{
    protected ?StreamInterface $stream = null;
    protected bool $hasFinishedStreaming = false;
    protected string $content = '';

    public function __construct(
        protected WrapperInterface $wrapper,
        protected string $prompt,
        protected array $previousMessages
    ) {
    }

    public function getType(): Type
    {
        return Type::Assistant;
    }

    public function getContent(?Closure $draw = null): string
    {
        if (!$this->stream) {
            $this->stream = $this->wrapper->prompt(
                'granite-4.1-8b',
                // 'qwen3.6-35b-a3b-mtp',
                $this->prompt,
                $this->previousMessages
            );
        }

        if ($this->hasFinishedStreaming) {
            return $this->content;
        }

        $part = $this->stream->next();

        if (!$part) {
            $this->hasFinishedStreaming = true;

            return $this->content;
        }

        if ($draw) {
            $draw();
        }

        $this->content .= $part->content;

        return $this->content;
    }

    public function hasFinishedStreaming(): bool
    {
        return $this->hasFinishedStreaming;
    }
}
