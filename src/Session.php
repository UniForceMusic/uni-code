<?php

namespace Src;

use Closure;
use Src\Messages\MessageInterface;
use Src\Messages\SystemMessage;
use Src\Widgets\MessageWidget;

class Session
{
    protected array $messages = [];

    public function __construct()
    {
        $this->messages[] = new SystemMessage(UniCode::SYSTEM_PROMPT);
    }

    public function appendMessage(MessageInterface $message): void
    {
        $this->messages[] = $message;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function countMessages(): int
    {
        return count($this->messages);
    }

    public function getMesssageWidgets(Closure $draw): array
    {
        return array_map(
            fn(MessageInterface $message) => new MessageWidget(
                $message->getType(),
                $message->getContent($draw)
            ),
            $this->messages
        );
    }

    public function getMessageCount(): int
    {
        return count($this->messages);
    }

    public function isReadyForNextPrompt(): bool
    {
        if (count($this->messages) === 0) {
            return true;
        }

        return end($this->messages)->hasFinishedStreaming();
    }
}
