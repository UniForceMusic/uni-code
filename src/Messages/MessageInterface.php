<?php

namespace Src\Messages;

use Closure;

interface MessageInterface
{
    public function getType(): Type;
    public function getContent(?Closure $draw = null): string;
    public function hasFinishedStreaming(): bool;
}
