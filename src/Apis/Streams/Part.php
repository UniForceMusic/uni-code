<?php

namespace Src\Apis\Streams;

class Part
{
    public function __construct(
        public Type $type,
        public string $content
    ) {
    }
}
