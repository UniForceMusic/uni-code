<?php

namespace Src\State;

use DateTime;

class NullableDateTimeState
{
    public function __construct(protected ?DateTime $value)
    {
    }

    public function get(): ?DateTime
    {
        return $this->value;
    }

    public function set(?DateTime $value): void
    {
        $this->value = $value;
    }
}
