<?php

namespace Src\Widgets;

use Closure;
use PhpTui\Term\Event;
use PhpTui\Tui\Extension\Core\Widget\Block\Padding;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\BorderType;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;
use Src\Apis\Wrappers\WrapperInterface;
use Src\Messages\AssistantMessage;
use Src\Messages\Type;
use Src\Messages\UserMessage;
use Src\Session;

class SessionWidget implements WidgetInterface
{
    protected Session $session;

    protected ChatWidget $chatWidget;
    protected PromptWidget $promptWidget;

    public function __construct(
        protected Closure $draw,
        WrapperInterface $wrapper
    ) {
        $this->session = new Session();

        $this->chatWidget = new ChatWidget($draw, $this->session);
        $this->promptWidget = new PromptWidget(
            $draw,
            executePrompt: function (Type $type, string $prompt) use ($wrapper): void {
                $previousMessages = $this->session->getMessages();

                $this->session->appendMessage(new UserMessage($prompt));

                $this->session->appendMessage(
                    new AssistantMessage(
                        $wrapper,
                        $prompt,
                        $previousMessages
                    )
                );
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
                    ->widget($this->chatWidget->toWidget($event)),
                BlockWidget::default()
                    ->padding(Padding::all(1))
                    ->borders(Borders::ALL)
                    ->borderType(BorderType::Rounded)
                    ->widget($this->promptWidget->toWidget($event)),
            );
    }
}
