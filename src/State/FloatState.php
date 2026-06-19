<?php

namespace Src\State;

class FloatState
{
    public function __construct(protected float $value)
    {
    }

    public function get(): float
    {
        return $this->value;
    }

    public function set(float $value): void
    {
        $this->value = $value;
    }
}
