<?php

namespace Src\Apis\Wrappers;

use BackedEnum;
use Src\Apis\Streams\StreamInterface;
use Src\Messages\MessageInterface;

interface WrapperInterface
{
    public function prompt(
        string|BackedEnum $model,
        string $prompt,
        /** @var MessageInterface[] */
        array $previousMessages = []
    ): StreamInterface;
}
