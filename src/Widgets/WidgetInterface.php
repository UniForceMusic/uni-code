<?php

namespace Src\Widgets;

use PhpTui\Term\Event;
use PhpTui\Term\Terminal;
use PhpTui\Tui\Widget\Widget;

interface WidgetInterface
{
    public function __construct(Terminal $terminal);
    public function toWidget(?Event $event): Widget;
}
