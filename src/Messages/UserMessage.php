<?php

namespace Src\Messages;

use Closure;
use Src\Author;

class UserMessage implements MessageInterface
{
    public function __construct(
        protected string $content
    ) {
    }

    public function getAuthor(): Author
    {
        return Author::User;
    }

    public function getContent(Closure $draw): string
    {
        return $this->content;
    }

    public function hasFinishedStreaming(): bool
    {
        return true;
    }
}
