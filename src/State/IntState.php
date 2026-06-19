<?php

namespace Src\State;

class IntState
{
    public function __construct(protected int $value)
    {
    }

    public function get(): int
    {
        return $this->value;
    }

    public function set(int $value): void
    {
        $this->value = $value;
    }
}
