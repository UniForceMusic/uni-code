<?php

namespace Src\Widgets;

use PhpTui\Term\Event;
use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PhpTui\Term\KeyModifiers;
use PhpTui\Term\Terminal;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Text\Text;
use PhpTui\Tui\Widget\Widget;

class PromptWidget implements WidgetInterface
{
    protected string $prompt = '';

    public function __construct(
        protected Terminal $terminal
    ) {
    }

    public function toWidget(?Event $event): Widget
    {
        if ($event instanceof CharKeyEvent) {
            $char = $event->char;
            $uppercase = $event->modifiers === KeyModifiers::SHIFT;

            $this->prompt .= $uppercase ? strtoupper($char) : $char;
        }

        if ($event instanceof CodedKeyEvent) {
            if ($event->code === KeyCode::Backspace) {
                $this->prompt = substr($this->prompt, 0, -1);
            }
        }

        return ParagraphWidget::fromText(
            Text::parse("<fg=blue>$this->prompt</>")
        );
    }
}
