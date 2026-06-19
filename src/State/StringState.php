<?php

namespace Src\State;

class StringState
{
    public function __construct(protected string $value)
    {
    }

    public function get(): string
    {
        return $this->value;
    }

    public function set(string $value): void
    {
        $this->value = $value;
    }
}
