<?php

namespace Src\Widgets;

use PhpTui\Term\Event;
use PhpTui\Tui\Widget\Widget;

interface WidgetInterface
{
    public function toWidget(?Event $event): Widget;
}
