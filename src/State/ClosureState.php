<?php

namespace Src\State;

use Closure;

class ClosureState
{
    protected Closure $value;

    public function __construct(callable|Closure $value)
    {
        $this->value = $value instanceof Closure ? $value : Closure::fromCallable($value);
    }

    public function get(): Closure
    {
        return $this->value;
    }

    public function set(Closure $value): void
    {
        $this->value = $value;
    }
}
