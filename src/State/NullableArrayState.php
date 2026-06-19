<?php

namespace Src\State;

class NullableArrayState
{
    public function __construct(protected ?array $value)
    {
    }

    public function get(): ?array
    {
        return $this->value;
    }

    public function set(?array $value): void
    {
        $this->value = $value;
    }
}
