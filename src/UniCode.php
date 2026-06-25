<?php

namespace Src;

use PhpTui\Term\Event;
use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PhpTui\Term\KeyModifiers;
use PhpTui\Term\Terminal;
use PhpTui\Tui\Display\Display;
use Src\Apis\Wrappers\OpenAIWrapper;
use Src\Widgets\UniCodeWidget;

class UniCode
{
    public const string SYSTEM_PROMPT = 'You are Uni-Code, an agent harness built by UniForceMusic. Your job is being an expert at coding';

    protected int $remainingDraws = 1;

    public static function fromPhpTui(Terminal $terminal, Display $display): static
    {
        return new static($terminal, $display);
    }

    public function __construct(
        protected Terminal $terminal,
        protected Display $display
    ) {
        $terminal->enableRawMode();
        $terminal->flush();

        $display->clear();
    }

    public function execute(int $fps = 60): int
    {
        $usleep = (int) (1000000 / $fps);

        $wrapper = new OpenAIWrapper('http://localhost:1234/v1', 'abcdefgh12345678');

        $uniCodeWidget = new UniCodeWidget(fn() => $this->queueDraw(), $wrapper);

        while (true) {
            $event = $this->terminal->events()->next();

            if ($event) {
                $this->queueDraw();

                if ($this->shouldExit($event)) {
                    break;
                }
            }

            $widget = $uniCodeWidget->toWidget($event);

            if ($this->remainingDraws > 0) {
                $this->display->draw($widget);
            }

            usleep($usleep);
        }

        return 0;
    }

    protected function shouldDraw(): bool
    {
        return $this->remainingDraws > 0;
    }

    protected function queueDraw(): void
    {
        $this->remainingDraws = 2;
    }

    protected function shouldExit(Event $event): bool
    {
        return (
            $event instanceof CharKeyEvent
            && $event->char === 'c'
            && $event->modifiers === KeyModifiers::CONTROL
        ) || (
            $event instanceof CodedKeyEvent
            && $event->code === KeyCode::Esc
        );
    }
}
