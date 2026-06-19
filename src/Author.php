<?php

namespace Src;

enum Author: string
{
    case User = 'user';
    case Model = 'model';
    case Tool = 'tool';

    public function color(): string
    {
        return match ($this) {
            self::User => 'blue',
            self::Model => 'green',
            self::Tool => 'yellow'
        };
    }
}
