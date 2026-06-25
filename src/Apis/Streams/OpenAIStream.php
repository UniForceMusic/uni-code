<?php

namespace Src\Apis\Streams;

use OpenAI\Responses\StreamResponse;

class OpenAIStream implements StreamInterface
{
    public function __construct(
        protected StreamResponse $stream
    ) {
    }

    public function next(): ?Part
    {
        foreach ($this->stream as $generator) {
            if (count($generator->choices) === 0) {
                continue;
            }

            return new Part(
                Type::Answer,
                $generator->choices[0]->delta->content ?? ''
            );
        }

        return null;
    }
}
