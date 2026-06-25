<?php

namespace Src\Messages;

enum Type: string
{
    case System = 'system';
    case User = 'user';
    case Assistant = 'assistant';
    case Tool = 'tool';

    public function color(): string
    {
        return match ($this) {
            self::System => 'white',
            self::User => 'blue',
            self::Assistant => 'green',
            self::Tool => 'yellow'
        };
    }
}
