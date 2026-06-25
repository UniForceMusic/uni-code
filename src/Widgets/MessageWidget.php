<?php

namespace Src\Widgets;

use PhpTui\Term\Event;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Text\Text;
use PhpTui\Tui\Widget\Widget;
use Src\Messages\Type;
use Src\Messages\Author;

class MessageWidget implements WidgetInterface
{
    public function __construct(
        protected Type $type,
        protected string $message
    ) {
    }

    public function toWidget(?Event $event): Widget
    {
        return ParagraphWidget::fromText(
            Text::parse(
                sprintf(
                    '<fg=%s>%s</>: %s',
                    $this->type->color(),
                    $this->type->value,
                    $this->message
                )
            )
        );
    }
}
