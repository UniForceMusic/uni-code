<?php

namespace Src\Apis\Streams;

interface StreamInterface
{
    public function next(): ?Part;
}
