<?php

namespace Src\Widgets;

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
use PhpTui\Tui\Text\Title;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\BorderType;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;

class UniCodeWidget implements WidgetInterface
{
    protected PromptWidget $promptWidget;

    public function __construct(
        protected Terminal $terminal
    ) {
        $this->promptWidget = new PromptWidget($terminal);
    }

    public function toWidget(?Event $event): Widget
    {
        return GridWidget::default()
            ->constraints(
                Constraint::percentage(10),
                Constraint::percentage(90),
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
                            ->padding(Padding::all(2))
                            ->borders(Borders::ALL)
                            ->borderType(BorderType::Rounded)
                            ->widget($this->promptWidget->toWidget($event)),
                        BlockWidget::default()
                            ->padding(Padding::all(2))
                            ->borders(Borders::ALL)
                            ->borderType(BorderType::Rounded)
                            ->widget(
                                CanvasWidget::fromIntBounds(-180, 180, -90, 90)
                                    ->draw(
                                        MapShape::default()->resolution(MapResolution::High)
                                    ),
                            ),
                    )
            );
    }
}
