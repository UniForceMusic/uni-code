<?php

namespace Src\Widgets;

use PhpTui\Term\Event;
use PhpTui\Term\Terminal;
use PhpTui\Tui\Extension\Core\Widget\Block\Padding;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\BorderType;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;

class SessionWidget implements WidgetInterface
{
    protected ChatWidget $sessionWidget;
    protected PromptWidget $promptWidget;

    public function __construct(
        protected Terminal $terminal
    ) {
        $this->sessionWidget = new ChatWidget($terminal);
        $this->promptWidget = new PromptWidget($terminal);
    }

    public function toWidget(?Event $event): Widget
    {
        return GridWidget::default()
            ->direction(Direction::Vertical)
            ->constraints(
                Constraint::percentage(75),
                Constraint::percentage(25)
            )
            ->widgets(
                BlockWidget::default()
                    ->padding(Padding::all(1))
                    ->borders(Borders::ALL)
                    ->borderType(BorderType::Rounded)
                    ->widget($this->sessionWidget->toWidget($event)),
                BlockWidget::default()
                    ->padding(Padding::all(1))
                    ->borders(Borders::ALL)
                    ->borderType(BorderType::Rounded)
                    ->widget($this->promptWidget->toWidget($event)),
            );
    }
}
