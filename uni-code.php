<?php

declare(strict_types=1);

use PhpTui\Term\Event\CharKeyEvent;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PhpTui\Term\KeyModifiers;
use PhpTui\Term\Terminal;
use PhpTui\Tui\DisplayBuilder;
use Src\Widgets\UniCodeWidget;

require 'vendor/autoload.php';

error_reporting(E_ERROR);

$terminal = Terminal::new();
$terminal->enableRawMode();
$terminal->flush();

$display = DisplayBuilder::default()->build();
$display->clear();

$uniCode = new UniCodeWidget($terminal);

while (true) {
    $event = $terminal->events()->next();

    if ($event) {
        if ($event instanceof CharKeyEvent) {
            if ($event->char === 'c' && $event->modifiers === KeyModifiers::CONTROL) {
                $terminal->flush();
                $display->clear();
                exit;
            }
        }

        if ($event instanceof CodedKeyEvent) {
            if ($event->code === KeyCode::Esc) {
                $terminal->flush();
                $display->clear();
                exit;
            }
        }
    }

    $display->draw($uniCode->toWidget($event));

    usleep(10000);
}
