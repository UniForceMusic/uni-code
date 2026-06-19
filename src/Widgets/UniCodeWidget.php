<?php

namespace Src\Widgets;

use Closure;
use PhpTui\Term\Event;
use PhpTui\Term\Terminal;
use PhpTui\Tui\Extension\Core\Shape\MapResolution;
use PhpTui\Tui\Extension\Core\Shape\MapShape;
use PhpTui\Tui\Extension\Core\Widget\Block\Padding;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\CanvasWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Text\Text;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\BorderType;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;

class UniCodeWidget implements WidgetInterface
{
    protected SessionWidget $sessionWidget;

    public function __construct(
        protected Closure $draw
    ) {
        $this->sessionWidget = new SessionWidget($draw);
    }

    public function toWidget(?Event $event): Widget
    {
        return GridWidget::default()
            ->direction(Direction::Vertical)
            ->constraints(
                Constraint::percentage(5),
                Constraint::percentage(90),
                Constraint::percentage(5),
            )
            ->widgets(
                ParagraphWidget::fromText(Text::parse(PHP_EOL)),
                GridWidget::default()
                    ->direction(Direction::Horizontal)
                    ->constraints(
                        Constraint::percentage(75),
                        Constraint::percentage(25)
                    )
                    ->widgets(
                        BlockWidget::default()
                            ->padding(Padding::horizontal(1))
                            ->borders(Borders::ALL)
                            ->borderType(BorderType::Rounded)
                            ->widget($this->sessionWidget->toWidget($event)),
                        BlockWidget::default()
                            ->borders(Borders::ALL)
                            ->borderType(BorderType::Rounded)
                            ->widget(
                                CanvasWidget::fromIntBounds(-180, 180, -90, 90)
                                    ->draw(
                                        MapShape::default()->resolution(MapResolution::High)
                                    ),
                            ),
                    ),
                ParagraphWidget::fromText(Text::parse(PHP_EOL))
            );
    }
}
