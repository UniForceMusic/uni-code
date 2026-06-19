<?php

namespace Src\State;

use Closure;

class NullableClosureState
{
    protected ?Closure $value;

    public function __construct(null|callable|Closure $value)
    {
        $this->value = $value instanceof Closure || $value === null ? $value : Closure::fromCallable($value);
    }

    public function get(): ?Closure
    {
        return $this->value;
    }

    public function set(?Closure $value): void
    {
        $this->value = $value;
    }
}
