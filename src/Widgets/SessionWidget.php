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
use Src\Messages\ModelMessage;
use Src\Messages\UserMessage;
use Src\Session;
use Src\State\ArrayState;

class SessionWidget implements WidgetInterface
{
    protected Session $session;

    protected ChatWidget $sessionWidget;
    protected PromptWidget $promptWidget;

    public function __construct(
        protected Closure $draw
    ) {
        $this->session = new Session();

        $this->sessionWidget = new ChatWidget($draw, $this->session);
        $this->promptWidget = new PromptWidget(
            $draw,
            executePrompt: function (Author $author, string $prompt): void {
                $this->session->appendMessage(new UserMessage($prompt));
                $this->session->appendMessage(new ModelMessage($prompt, 'No system prompt yet'));
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
