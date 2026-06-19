<?php

namespace Src\Widgets;

use PhpTui\Term\Event;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;
use Src\State\ArrayState;

class ChatWidget implements WidgetInterface
{
    public function __construct(
        protected ArrayState $messages
    ) {
    }

    public function toWidget(?Event $event): Widget
    {
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
