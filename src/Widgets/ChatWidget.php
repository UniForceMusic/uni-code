<?php

namespace Src\Widgets;

use PhpTui\Term\Event;
use PhpTui\Term\Terminal;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Extension\Core\Widget\ScrollbarWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Text\Text;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;

class ChatWidget implements WidgetInterface
{
    protected array $messages = [
        '<fg=blue>User:</> Please do this',
        '<fg=green>AI:</> Okay i will do it',
        '<fg=blue>User:</> Please do this',
        '<fg=green>AI:</> Okay i will do it',
        '<fg=blue>User:</> Please do this',
    ];

    public function __construct(
        protected Terminal $terminal
    ) {
    }

    public function toWidget(?Event $event): Widget
    {
        return GridWidget::default()
            ->direction(Direction::Vertical)
            ->constraints(
                ...array_map(
                    fn() => Constraint::percentage(10),
                    $this->messages
                )

            )
            ->widgets(
                ...array_map(
                    fn(string $message) => ParagraphWidget::fromText(
                        Text::parse($message)
                    ),
                    $this->messages
                )
            );
    }
}
