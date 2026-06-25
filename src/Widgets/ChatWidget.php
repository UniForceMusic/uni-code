<?php

namespace Src\Widgets;

use Closure;
use PhpTui\Term\Event;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;
use Src\Session;

class ChatWidget implements WidgetInterface
{
    public function __construct(
        protected Closure $draw,
        protected Session $session
    ) {
    }

    public function toWidget(?Event $event): Widget
    {
        return GridWidget::default()
            ->direction(Direction::Vertical)
            ->constraints(
                ...array_fill(
                    0,
                    $this->session->getMessageCount() - 1,
                    Constraint::percentage(10),
                )

            )
            ->widgets(
                ...array_map(
                    fn(WidgetInterface $widget) => $widget->toWidget($event),
                    array_slice($this->session->getMesssageWidgets($this->draw), 1)
                )
            );
    }
}
