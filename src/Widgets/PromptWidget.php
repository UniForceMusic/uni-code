<?php

namespace Src\Widgets;

use Closure;
use PhpTui\Term\Event;
use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PhpTui\Term\KeyModifiers;
use PhpTui\Term\Terminal;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Text\Text;
use PhpTui\Tui\Widget\Widget;
use Src\Messages\Author;
use Src\Messages\Type;

class PromptWidget implements WidgetInterface
{
    protected string $prompt = '';

    public function __construct(
        protected Closure $draw,
        protected Closure $executePrompt
    ) {
    }

    public function toWidget(?Event $event): Widget
    {
        if ($event && $event instanceof CharKeyEvent) {
            $char = $event->char;
            $uppercase = $event->modifiers === KeyModifiers::SHIFT;

            $this->prompt .= $uppercase ? strtoupper($char) : $char;
        }

        if ($event && $event instanceof CodedKeyEvent) {
            if ($event->code === KeyCode::Backspace) {
                $this->prompt = substr($this->prompt, 0, -1);
            }

            if ($event->code === KeyCode::Enter) {
                ($this->executePrompt)(Type::User, $this->prompt);

                $this->prompt = '';
            }
        }

        return ParagraphWidget::fromText(
            Text::parse(
                sprintf(
                    '<fg=%s>%s</>',
                    Type::User->color(),
                    $this->prompt
                )
            )
        );
    }
}
