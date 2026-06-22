<?php

namespace Src\Messages;

use Closure;
use Src\Author;

interface MessageInterface
{
    public function getAuthor(): Author;
    public function getContent(Closure $draw): string;
    public function hasFinishedStreaming(): bool;
}
