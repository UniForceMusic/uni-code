<?php

namespace Src\State;

class BoolState
{
    public function __construct(protected bool $value)
    {
    }

    public function get(): bool
    {
        return $this->value;
    }

    public function set(bool $value): void
    {
        $this->value = $value;
    }
}
