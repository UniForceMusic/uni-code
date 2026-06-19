<?php

namespace Src\Widgets;

use Closure;
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
use Src\Author;
use Src\State\ArrayState;

class SessionWidget implements WidgetInterface
{
    protected ArrayState $messages;

    protected ChatWidget $sessionWidget;
    protected PromptWidget $promptWidget;

    public function __construct(
    ) {
        $this->messages = new ArrayState([]);

        $this->sessionWidget = new ChatWidget($this->messages);
        $this->promptWidget = new PromptWidget(
            executePrompt: function (Author $author, string $prompt): void {
                $messages = $this->messages->get();

                array_push($messages, new MessageWidget($author, $prompt));
                array_push($messages, new MessageWidget(Author::Model, 'There is no model connected yet'));

                $this->messages->set($messages);
            }
        );
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
