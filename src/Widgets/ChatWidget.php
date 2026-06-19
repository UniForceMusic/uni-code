<?php

namespace Src\Widgets;

use Closure;
use PhpTui\Term\Event;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;
use Src\State\ArrayState;

class ChatWidget implements WidgetInterface
{
    protected ?int $messagesCount = null;

    public function __construct(
        protected Closure $draw,
        protected ArrayState $messages
    ) {
        if ($this->messagesCount === null) {
            $this->messagesCount = count($messages->get());
        }
    }

    public function toWidget(?Event $event): Widget
    {
        if (count($this->messages->get()) !== $this->messagesCount) {
            $this->messagesCount = count($this->messages->get());

            ($this->draw)();
        }

        return GridWidget::default()
            ->direction(Direction::Vertical)
            ->constraints(
                ...array_map(
                    fn() => Constraint::percentage(10),
                    $this->messages->get()
                )

            )
            ->widgets(
                ...array_map(
                    fn(WidgetInterface $widget) => $widget->toWidget($event),
                    $this->messages->get()
                )
            );
    }
}
