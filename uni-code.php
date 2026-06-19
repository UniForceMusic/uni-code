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

$draw = true;
// Rewrite to more robust solution
$drawNextLoop = false;

$terminal = Terminal::new();
$terminal->enableRawMode();
$terminal->flush();

$display = DisplayBuilder::default()->build();
$display->clear();

$uniCode = new UniCodeWidget(fn() => $draw = true);

while (true) {
    $event = $terminal->events()->next();

    $widget = $uniCode->toWidget($event);

    if ($drawNextLoop) {
        $drawNextLoop = false;

        $display->draw($widget);
    }

    if ($event) {
        $draw = true;

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

    if ($draw) {
        $draw = false;
        $drawNextLoop = true;

        $display->draw($widget);
    }

    usleep(10000);
}
