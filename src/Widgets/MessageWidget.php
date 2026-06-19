<?php

namespace Src\Widgets;

use PhpTui\Term\Event;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Text\Text;
use PhpTui\Tui\Widget\Widget;
use Src\Author;

class MessageWidget implements WidgetInterface
{
    public function __construct(
        protected Author $author,
        protected string $message
    ) {
    }

    public function toWidget(?Event $event): Widget
    {
        return ParagraphWidget::fromText(
            Text::parse(
                sprintf(
                    '<fg=%s>%s</>: %s',
                    $this->author->color(),
                    $this->author->value,
                    $this->message
                )
            )
        );
    }
}
