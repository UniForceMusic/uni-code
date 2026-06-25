<?php

namespace Src\Messages;

use Closure;

class SystemMessage implements MessageInterface
{
    public function __construct(
        protected string $content,
    ) {
    }

    public function getType(): Type
    {
        return Type::System;
    }

    public function getContent(?Closure $draw = null): string
    {
        return $this->content;
    }

    public function hasFinishedStreaming(): bool
    {
        return true;
    }
}
